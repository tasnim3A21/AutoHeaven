<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class MailerService
{
    private MailerInterface $mailer;
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, Connection $connection, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function sendEmail(string $recipient, string $subject, string $message, string $sender = 'admin', string $receiver = 'client', ?int $idRec = null, ?int $idUser = null): void
    {
        $this->logger->info('Début de l\'envoi de l\'email à {recipient}', ['recipient' => $recipient]);

        $email = (new Email())
            ->from('noreply@tonsite.com')
            ->to($recipient)
            ->subject($subject)
            ->text($message);

        try {
            $this->mailer->send($email);
            $this->logger->info('Email envoyé avec succès à {recipient}', ['recipient' => $recipient]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email à {recipient} : {error}', [
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Erreur inattendue lors de l\'envoi de l\'email à {recipient} : {error}', [
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Insérer directement dans la table messagerie après l'envoi de l'e-mail
        $this->logger->info('Enregistrement du message dans la table messagerie pour id_rec={id_rec}', ['id_rec' => $idRec]);
        $sql = 'INSERT INTO messagerie (sender, message, id_rec, datemessage, receiver, id_user) VALUES (:sender, :message, :id_rec, :datemessage, :receiver, :id_user)';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'sender' => $sender,
            'message' => $message,
            'id_rec' => $idRec,
            'datemessage' => (new \DateTime())->format('Y-m-d H:i:s'),
            'receiver' => $receiver,
            'id_user' => $idUser,
        ]);
        $this->logger->info('Message enregistré avec succès dans messagerie pour id_rec={id_rec}', ['id_rec' => $idRec]);
    }
}
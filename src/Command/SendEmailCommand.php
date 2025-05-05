<?php

namespace App\Command;

use App\Service\MailerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:send-email',
    description: 'Sends a test email using MailerService',
)]
class SendEmailCommand extends Command
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        parent::__construct();
        $this->mailerService = $mailerService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->mailerService->sendEmail(
            'abidiahlemea@gmail.com',
            'Test Email Subject',
            'This is a test email sent from MailerService!',
            'admin',
            'client',
            112, // ID qui existe dans la table reclamation
            null
        );
        $output->writeln('Email sent successfully and saved to messagerie table!');
        return Command::SUCCESS;
    }
}
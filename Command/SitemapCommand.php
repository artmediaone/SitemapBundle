<?php

namespace Amo\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('amo:sitemap:generate')
                ->setDescription('Generate sitemap file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Processing ...</info>');
        $this->getContainer()->get('amo_sitemap.manager')->generate();
        $output->writeln('<info>Finished successfully.</info>');
    }
}

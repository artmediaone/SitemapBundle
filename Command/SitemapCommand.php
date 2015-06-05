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
                ->setDescription('Generate sitemap file')
                ->addArgument('base_url', null, InputArgument::REQUIRED, 'base url used to generate absolute urls, ex. http://www.example.com')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base_url = $input->getArgument('base_url');
        
        if(filter_var($base_url,  FILTER_VALIDATE_URL) === false){
            $output->writeln('<error>invalid base url, please make sure its full url; ex. http://example.com</error>');
            return;
        }
        
        $output->writeln('<info>Processing ...</info>');
        $this->getContainer()->get('amo_sitemap.manager')->generate($base_url);
        $output->writeln('<info>Finished successfully.</info>');
    }
}

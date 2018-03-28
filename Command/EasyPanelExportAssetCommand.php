<?php
/**
 * Created by marcosamano.
 * Date: 24/03/18
 */
namespace Ast\EasyPanelBundle\Command;

use Ast\EasyPanelBundle\Lib\Crud\EasyPanelCreate;
use Ast\EasyPanelBundle\Lib\Crud\EasyPanelCreateSato;
use Ast\EasyPanelBundle\Lib\Crud\Lib\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
// Add the Container
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use ZipArchive;

class EasyPanelExportAssetCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('easypanel:export:assets')
            ->setDescription('Exporta los asset a la carpeta admin.')
            ->addOption('dir',null,InputOption::VALUE_REQUIRED,'Especifica el directorio Web','web')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $dir = $input->getOption('dir');
        $init = $this->getContainer()->getParameter("kernel.root_dir").'/../'.$dir;
        $path = Util::createDir($init."/");
        $filezip = $path.'/admin.zip';

        $output->writeln([
            'Create Assets to EasyPanel',// A line
            '========================================',// Another line
            '',// Empty line
        ]);

        // outputs a message followed by a "\n"
        $output->writeln('Directorio: '.$dir);
        $output->writeln('Salida: '.$path);
        $output->writeln('');
        if(copy (  __DIR__.'/admin.zip' ,  $filezip )){
            $zip = new ZipArchive();
            $res = $zip->open($filezip);
            if ($res === TRUE) {
                $zip->extractTo($path.'/admin/');
                $zip->close();
                unlink($filezip);
                $output->writeln('Assets export '.$dir.'/admin');
            }else{
                $output->writeln('Copy to  '.$dir.'/admin.zip unzip file ');
            }
        }else{
            $output->writeln('Sorry please use "https://startbootstrap.com/template-overviews/sb-admin"');
        }



        // outputs a message without adding a "\n" at the end of the line
        $output->writeln(['','Comando Terminado, :)']);
    }
}
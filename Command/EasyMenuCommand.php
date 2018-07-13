<?php
/**
 * Created by marcosamano.
 * Date: 24/03/18
 */
namespace Ast\EasyPanelBundle\Command;

use Ast\EasyPanelBundle\Lib\Crud\EasyPanelCreate;
use Ast\EasyPanelBundle\Lib\Crud\EasyPanelCreateInit;
use Ast\EasyPanelBundle\Lib\Crud\EasyPanelMenu;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
// Add the Container
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class EasyMenuCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('easypanel:create:menu')
            ->setDescription('Crea un twig de menu usando entidades.')
            ->addArgument('proyecto', InputArgument::REQUIRED, 'Nombre del proyecto')
            ->addArgument('panelbundle', InputArgument::REQUIRED, 'Bundle donde estara el panel')
            ->addArgument('entitybundle', InputArgument::REQUIRED, 'Bundle donde estan las entidades')
            ->addArgument('prefix', InputArgument::REQUIRED, 'Prefijo para las rutas')
            ->addOption('menu_collapse',null,InputOption::VALUE_REQUIRED,'Menu collapsado (1,0)','1')


        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)

        $em = $this->getContainer()->get('doctrine')->getManager();
        $twig = $this->getContainer()->get('twig');
        $dir = $this->getContainer()->getParameter("kernel.root_dir").'/../src/';
        $proyecto = $input->getArgument('proyecto');
        $panelbundle = $input->getArgument('panelbundle');
        $entitybundle = $input->getArgument('entitybundle');
        $prefix = $input->getArgument('prefix');

        if($input->getOption('menu_collapse')=='1'){
            $menu= EasyPanelCreate::MENU_COLLAPSE;
        }elseif($input->getOption('menu_collapse')=='0'){
            $menu= EasyPanelCreate::MENU_EXPAND;
        }else{
            $menu= EasyPanelCreate::MENU_COLLAPSE;
        }
        $output->writeln([
            'Create EasyPanel Menu  ',// A line
            '========================================',// Another line
            '',// Empty line
        ]);

        // outputs a message followed by a "\n"
        $output->writeln('Proyecto: '.$proyecto);
        $output->writeln('PanelBundle: '.$panelbundle);
        $output->writeln('EntityBundle: '.$entitybundle);
        $output->writeln('Prefix: '.$prefix);
        $output->writeln('Tipo Menu: '.$menu);
        $output->writeln('');

        $panel = new EasyPanelMenu($em,$twig,$dir,$proyecto,$panelbundle,$entitybundle,$prefix);
        $resultado = $panel->create($menu);
        $output->writeln('Resultado:'.$resultado);


        // outputs a message without adding a "\n" at the end of the line
        $output->writeln(['','Comando Terminado, :)']);
    }
}
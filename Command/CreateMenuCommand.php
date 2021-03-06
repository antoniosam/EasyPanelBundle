<?php
/**
 * Created by marcosamano.
 * Date: 24/03/18
 */
namespace Ast\EasyPanelBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Doctrine\ORM\EntityManager;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Ast\EasyPanelBundle\Lib\Crud\EasyPanelMenu;
use Ast\EasyPanelBundle\Lib\Crud\Utils\InfoEntityImport;
use Ast\EasyPanelBundle\Lib\Crud\Utils\Util;

class CreateMenuCommand extends Command
{
    protected static $defaultName = 'easypanel:create:menu';

    private $twigExtension;
    private $em;
    private $params;

    public function __construct(Environment $twigExtension, EntityManager $entityManager,ParameterBagInterface $params)
    {
        $this->twigExtension = $twigExtension;
        $this->em = $entityManager;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(static::$defaultName)
            ->setDescription('Crea un twig de menu usando entidades.')
            ->addArgument('proyecto', InputArgument::REQUIRED, 'Nombre del proyecto')
            ->addArgument('directorio_entitys', InputArgument::REQUIRED, 'Carpeta donde se ubican las entidades des src')
            ->addOption('prefix',null,InputOption::VALUE_REQUIRED,'Prefijo para las rutas')
            ->addOption('folder', null,InputOption::VALUE_REQUIRED, 'Carpeta donde se generan los archivos dentro de la estructura de Symfony')
            ->addOption('tema',null,InputOption::VALUE_REQUIRED,'Tipo de assets (material, sb-admin)','material')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tiempo_inicio = microtime(true);

        $proyecto = $input->getArgument('proyecto');
        $directorio_entitys = $input->getArgument('directorio_entitys');

        $tema = $input->getOption('tema');
        $folder = $input->getOption('folder');
        $prefix = $input->getOption('prefix');

        $rootDir = $this->params->get('kernel.project_dir').DIRECTORY_SEPARATOR.'src';
        $folder = strtolower($folder);

        $output->writeln([
            'Create EasyPanel Menu  ',// A line
            '========================================',// Another line
            '',// Empty line
        ]);

        // outputs a message followed by a "\n"
        $output->writeln('Proyecto: '.$proyecto);

        $output->writeln('Directorio Entidades: '.$directorio_entitys);
        $output->writeln('Prefix: '.$prefix);
        //$output->writeln('Tipo Menu: '.$menu);
        $output->writeln('');
        $output->writeln('Directorio de busqueda:'.$rootDir.DIRECTORY_SEPARATOR.$directorio_entitys);

        $listaclases = InfoEntityImport::folder($this->em,$rootDir.DIRECTORY_SEPARATOR.$directorio_entitys);
        if(count($listaclases) > 0) {
            $listaentitys = [];
            foreach ($listaclases as $clase):
                $entity = Util::getFileNamespace($clase);
                $listaentitys[] = $entity;
            endforeach;
            $panel = new EasyPanelMenu($this->em,$this->twigExtension,$rootDir,$proyecto,$listaentitys,$prefix,$folder,$tema);
            $resultado = $panel->create();
            $output->writeln('Resultado:'.$resultado);
        }else{
            $output->writeln('No se encontraron entidades');
        }
        // outputs a message without adding a "\n" at the end of the line
        $output->writeln(['','Comando Terminado, '.$this->timecommand($tiempo_inicio).' :)']);
        return 0;
    }

    private function timecommand($tiempo_inicio){
        $tiempo_fin = microtime(true);
        $seconds = round($tiempo_fin - $tiempo_inicio, 0);
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        if($secs == 0){
            $secs = round($tiempo_fin - $tiempo_inicio, 3);
        }

        return ($hours>0? $hours.'h ':'').($mins>0? $mins.'m ':''). $secs.'s';
    }
}
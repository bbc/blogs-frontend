<?php
declare(strict_types = 1);
namespace App\Twig;

use Twig_Extension;
use Twig_Function;

class SmpExtension extends Twig_Extension
{
    private $smps;

    public function __construct()
    {
        $this->smps = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('add_smp', [$this, 'addSmp']),
            new Twig_Function('build_smps', [$this, 'buildSmps']),
        ];
    }

    public function addSmp(string $player)
    {
        $this->smps[] = $player;
    }

    public function buildSmps(): string
    {
        if (empty($this->smps)) {
            return '';
        }
        $smps = 'require([\'smp\'], function(SMP) {';
        foreach ($this->smps as $player) {
            $smps .= 'new SMP(' . $player . ');';
        }
        $smps .= '});';
        return $smps;
    }
}

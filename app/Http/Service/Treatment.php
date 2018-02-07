<?php

namespace App\Http\Service;

use Illuminate\Http\Response;
use Symfony\Component\Process\Process;

class Treatment
{
    const DIR_NAME = "scan";

    private function getPathStorage()
    {
        return \Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().self::DIR_NAME;
    }

    public function gitClone($url){

        \Storage::deleteDirectory(self::DIR_NAME);
        \Storage::makeDirectory(self::DIR_NAME);

        $process = new Process('git clone '.$url.' '. $this->getPathStorage());
        $process->run();


        if (!$process->isSuccessful()){
            echo "Envoi mail utilisateur ERROR"; // TODO
            return new Response(["error" => "Git repository"], Response::HTTP_NOT_FOUND);
        }

        $this->phpstan();

    }

    public function phpstan()
    {
        $process = new Process('/var/www/api.hackaton/vendor/bin/phpstan analyse '.$this->getPathStorage());
        $process->run();

        dd($process->getOutput());

    }

}

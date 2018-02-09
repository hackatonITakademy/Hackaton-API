<?php

namespace App\Http\Service;

use Illuminate\Http\Response;
use Symfony\Component\Process\Process;

class Treatment
{
    const DIR_NAME = "scan";
    const DIR_FILE = "files";

    private function getPathStorage()
    {
        return \Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . self::DIR_NAME;
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

        return $this->phpcs();

    }


    public function phpcs()
    {

        $rules =   ["PEAR.ControlStructures.MultiLineCondition.NewlineBeforeOpenBrace",
            "PEAR.NamingConventions.ValidFunctionName.NotCamelCaps",
            "PEAR.Commenting.FunctionComment.WrongStyle",
            "PEAR.Commenting.FileComment.Missing",
            "PEAR.Commenting.ClassComment.Missing",
            "PEAR.Classes.ClassDeclaration.OpenBraceNewLine",
            "Generic.WhiteSpace.DisallowTabIndent.TabsUsed",
            "PEAR.WhiteSpace.ScopeIndent.IncorrectExact",
            "PEAR.WhiteSpace.ScopeIndent.Incorrect",
            "Generic.Functions.FunctionCallArgumentSpacing.NoSpaceAfterComma",
            "PEAR.Functions.FunctionDeclaration.BraceOnSameLine",
            "PEAR.Commenting.FunctionComment.Missing",
            "PEAR.ControlStructures.ControlSignature.Found",
            "PEAR.ControlStructures.MultiLineCondition.SpaceBeforeOpenBrace",
            "PEAR.WhiteSpace.ScopeClosingBrace.Line",
            "Generic.WhiteSpace.DisallowTabIndent.NonIndentTabsUsed",
            "Generic.Files.LineEndings.InvalidEOLChar",
            "PEAR.Functions.FunctionCallSignature.SpaceBeforeOpenBracket",
            "Generic.Commenting.DocComment.TagValueIndent",
            "Generic.Commenting.DocComment.NonParamGroup",
            "PEAR.Commenting.FunctionComment.MissingParamComment",
            "PEAR.WhiteSpace.ScopeClosingBrace.BreakIndent",
            "Generic.Commenting.DocComment.ShortNotCapital",
            "PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps",
            "PEAR.Functions.FunctionCallSignature.SpaceAfterCloseBracket"
        ];


        $process = new Process('/var/www/api.hackaton/vendor/bin/phpcs --report=json --extensions=php --severity=5 '.$this->getPathStorage());
        $process->run();


        $tabResults = json_decode($process->getOutput())->files;

        $tabFinal = $tabResults;
        $totalErrors = 0;
        $totalFixable = 0;

        $fileOutPut = "<h1>Vérification des erreurs sur le projet</h1>";

        foreach ($tabResults as $e => $fichier){


            $cpt = 0;
            $errors = "";
            foreach($fichier->messages as $f => $message){

                if($message->type == "WARNING" || in_array($message->source, $rules)){
                    unset($tabFinal->{$e}->{'messages'}[$f]);
                }
                else
                {
                    $cpt++;
                    $errors .= "<li> Ligne ".$message->line.": ".$message->message."</li>";
                }
                if($message->fixable == true){
                    $totalFixable++;
                }
            }
            unset($tabFinal->{$e}->warnings);
            $tabFinal->{$e}->errors = $cpt;

            if($tabFinal->{$e}->errors == 0)
            {
                unset($tabFinal->{$e});
            }
            else
            {
                $fileOutPut .= "<hr>"."Fichier : ".$e."<hr>";
                $fileOutPut .= "<ul>".$errors."</ul>";
            }

            $totalErrors += $cpt;

        }
        $fileOutPut .= "<h2>".$totalFixable." erreur(s) corrigeable(s) sur un total de ".$totalErrors." erreur(s)</h2>";

        \Storage::makeDirectory(self::DIR_FILE);
        $filename = sha1(microtime()).".html";

        \Storage::put(self::DIR_FILE."/".$filename, $fileOutPut);

        return $filename;

        return $this->phpParaLint($fileOutPut);
        $this->phpParaLint($fileOutPut);

    }

    public function phpParaLint($content) //TODO: composer require jakub-onderka/php-parallel-lint
    {

        $process = new Process('/var/www/api.hackaton/vendor/bin/parallel-lint -e php --json '.$this->getPathStorage());

        $process->run();

        $fileOutput = $content . "<br><br>";
        $tabResults = json_decode($process->getOutput())->results;
        $totalErrors = 0;

        if(count(json_decode($process->getOutput())->results->errors) == 0){
            $fileOutput .=  "<h1>Félicitation, aucun problème de syntaxe détecté</h1>";
        }
        else
        {
            $fileOutput .=  "<h1>Problèmes de syntaxe</h1><hr><ul>";
            foreach($tabResults->errors as $f => $message){
                $fileOutput .= "<li> Ligne ".$message->line.": ".$message->message."</li>";
                $totalErrors ++;

            }
            $fileOutput .= "</ul>";
        }

        \Storage::makeDirectory(self::DIR_FILE);
        $filename = sha1(microtime()).".html";

        \Storage::put(self::DIR_FILE."/".$filename, $fileOutput);

//        dd($fileOutput);
        return $filename;
    }

}

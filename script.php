#!/usr/bin/php
<?php

abstract class Transpositor
{
    abstract public function transpose();
}

class PianoTranspositor extends Transpositor
{

    private array $inputNotes;
    private ?int $numberSemitones;
    private array $resultNotes;

    public function __construct(array $argv)
    {
        $this->inputNotes = !empty($argv[1]) ? json_decode(file_get_contents($argv[1])) : [];
        $this->numberSemitones = $argv[2] ?? null;
    }

    public function transpose(): void
    {
        try {
            $this->validateInputData();

            foreach ($this->inputNotes as $singleNote) {
                $this->transposeSingleNote($singleNote);

                if (!$this->noteIsValid($singleNote)) {
                    throw new Exception('Existing wrong note');
                }
            }

            $this->printResult();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * @throws Exception
     */
    private function validateInputData(): void
    {
        if (empty($this->inputNotes) || !isset($this->numberSemitones)) {
            throw new Exception('Invalid input data');
        }
    }

    private function transposeSingleNote(array $singleNote): void
    {
        [$octaveNumber, $noteNumber] = $singleNote;

        $transposedNoteNumber = ($noteNumber + $this->numberSemitones) % 12;
        $transposedOctave = $octaveNumber + floor(($noteNumber + $this->numberSemitones) / 12);

        if ($transposedNoteNumber <= 0) {
            $transposedNoteNumber += 12;
        }

        $this->resultNotes[] = [$transposedOctave, $transposedNoteNumber];
    }

    private function noteIsValid(array $note): bool
    {
        [$octave, $noteNumber] = $note;

        return $octave >= -3 && $octave <= 5 && $noteNumber >= 1 && $noteNumber <= 11;
    }


    private function printResult(): void
    {
        $outputData = json_encode($this->resultNotes);

        $filePath = 'result.json';

        $result = file_put_contents($filePath, $outputData);

        if ($result) {
            echo "The Result was written in result.json file.";
        } else {
            echo "Error occurred for writing result in the file.";
        }

        echo "\n";
    }
}

(new PianoTranspositor($argv))->transpose();

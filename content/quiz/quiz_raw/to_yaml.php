<?php

$file = 'quiz_04_2_010.txt';
$fullPath = __DIR__ . DIRECTORY_SEPARATOR . $file;
$content = file_get_contents($fullPath);

$questions = prepareQuestions($content);

$fullContent = '';
foreach ($questions as $key => $question) {
    $questionNum = $key + 1;

    $correct1 = $question["answers"][0]['isCorrect'] ? ' ✅' : '';
    $correct2 = $question["answers"][1]['isCorrect'] ? ' ✅' : '';
    $correct3 = $question["answers"][2]['isCorrect'] ? ' ✅' : '';
    $fullContent .= <<<CONTENT
⭐️Question {$questionNum}:
{$question["description"]}

1️⃣$correct1 Answer 1:
{$question["answers"][0]["content"]}
😎Why:
{$question["answers"][0]["explanation"]}
--

2️⃣$correct2 Answer 2:
{$question["answers"][1]["content"]}
😎Why:
{$question["answers"][1]["explanation"]}
--

3️⃣$correct3 Answer 3:
{$question["answers"][2]["content"]}
😎Why:
{$question["answers"][2]["explanation"]}

☁️DISCUSSED:
{$question["whereDiscussed"]}

##########


CONTENT;

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('.', '_', $file) . '_CLEAN_.txt';
}

file_put_contents($filePath, $fullContent);




//<editor-fold desc="Preparation">
function prepareQuestions($content) {
    $questionsRaw = extractSimpleDigitValue($content, 'Question');

    $questions = [];

    foreach ($questionsRaw as $questionRaw) {
        $description = extractSimpleValue($questionRaw, 'Description');

        $answersRaw = extractSimpleDigitValue($questionRaw, 'Answer');
        $correctAnswerNumber = 0;

        $answers = [];
        foreach ($answersRaw as $key => $answerRaw) {
            $isCorrect = in_array(strtolower(extractSimpleValue($answerRaw, 'Is Correct')), ['true', 'yes']);
            $content = extractSimpleValue($answerRaw, 'Content');
            $explanation = extractSimpleValue($answerRaw, 'Explanation');
            $answers[] = [
                'isCorrect' => $isCorrect,
                'content' => $content,
                'explanation' => $explanation,
            ];

            if ($isCorrect) {
                $correctAnswerNumber = $key;
            }

        }

        $questions[] = [
            'description' => $description,
            'answers' => $answers,
            'correctAnswerNumber' => $correctAnswerNumber,
            'whereDiscussed' => extractSimpleValue($questionRaw, 'Where discussed'),
        ];
    }

    return $questions;
}

function extractSimpleValue($content, $tagName) {
    $results = null;
    $regExp = "@\\<$tagName>(.*?)\\<\\/$tagName>@si";
    preg_match_all($regExp, $content, $results);
    return trim($results[1][0]);
}

function extractSimpleDigitValue($content, $tagName) {
    $results = null;
    $regExp = "@\\<$tagName \d+\>(.*?)\\<\\/$tagName \d+\>@si";
    preg_match_all($regExp, $content, $results);
    return ($results[1]);
}
//</editor-fold>

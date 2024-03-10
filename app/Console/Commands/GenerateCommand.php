<?php

namespace App\Console\Commands;

use App\Api\OpenAiApi;
use App\Enum\QuizType;
use App\Models\QuizQuestion;
use App\Prompts\GenerateQuestionToQuizPromptHelper;
use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:design-patterns {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generowanie nowych pytań do quizu o wzorcach projektowych';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        $bar = $this->output->createProgressBar($count * 2);
        $bar->start();

        $handmade = false;

        $errors = [];
        $success = 0;
        $failed = 0;

        for($i = 1; $i <= $count; $i++){
            try{
                $designPatterns = GenerateQuestionToQuizPromptHelper::getDesignPatterns();
                $randomIndex = array_rand($designPatterns);
                $chooseDesignPattern = $designPatterns[$randomIndex];

                $listCurrentQuiz = [];
                $savedQuizWithThisPattern = QuizQuestion::where('correct_answer', $chooseDesignPattern)->get();

                foreach ($savedQuizWithThisPattern as $quiz){
                    $listCurrentQuiz[] = substr($quiz->question, 0, 30);;
                }

                $prompt = GenerateQuestionToQuizPromptHelper::getPrompt($chooseDesignPattern, $listCurrentQuiz);

                if($handmade){
                    echo $prompt;
                    dd($prompt, GenerateQuestionToQuizPromptHelper::generateOptions($chooseDesignPattern), $chooseDesignPattern);
                }else{


                    $bar->advance();
                    /** @var OpenAiApi $openAiApi */
                    $openAiApi = app(OpenAiApi::class);
                    $json = $openAiApi->completionChat($prompt);
                    $json = GenerateQuestionToQuizPromptHelper::stripJsonMarkdown($json);

                    $quizJson = json_decode($json, true);
                    echo $json;


                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception(json_last_error_msg() . ' ' . $json);
                    }

                    if ($quizJson === null) {
                        throw new \Exception('Zawartość JSON jest pusta lub nieprawidłowa');
                    }


                    $quizJson['options'] = GenerateQuestionToQuizPromptHelper::generateOptions($chooseDesignPattern);
                    $quizJson['correctAnswer'] = $chooseDesignPattern;

                    $quiz = new QuizQuestion();
                    $quiz->type = QuizType::DESIGN_PATTERN->value;
                    $quiz->lang = 'pl';
                    $quiz->question = $quizJson['question'];
                    $quiz->options = $quizJson['options'];
                    $quiz->correct_answer = $quizJson['correctAnswer'];
                    $quiz->explanation = $quizJson['explanation'];

                    $quiz->save();
                    $success++;
                }
            }catch (\Exception $e){
                $errors[] = $e->getMessage();
                $quiz = new QuizQuestion();
                $failed ++;

                echo $e->getMessage();
            }

            $bar->advance();
        }


        echo '<h1>' . $success .' / ' . $failed . '</h1>';


        $bar->finish();
        return Command::SUCCESS;
    }
}

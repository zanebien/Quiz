<?php
	class QuizManager
	{
		private $conn;
		private static $instance;
		
		public static function getInstance(PDO $conn)
		{
			if(empty(self::$instance))
				self::$instance = new self($conn);
			return self::$instance;
		}
		
		private function __construct(PDO $conn)
		{
			$this->conn = $conn;
		}
		
		public function getQuiz($id)
		{
			if(empty($id))
				throw new Exception('No quiz ID given. Cannot retrieve quiz information.');
			
			$sql = 'SELECT quiz_id, quiz_name, quiz_description FROM quizes WHERE quiz_id = :id';
			$stmt = $this->conn->prepare($sql);
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			if(!$stmt->rowCount())
				throw new Exception('Quiz not found.');
			
			$info = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$quiz = new Quiz($info['quiz_id']);
			$quiz->setName($info['quiz_name']);
			$quiz->setDescription($info['quiz_description']);
			
			return $quiz;
		}
		
		public function getQuestions(Quiz &$quiz)
		{
			$qid = $quiz->id();
			
			if(empty($qid))
				throw new Exception('No quiz ID given. Cannot retrieve quiz questions.');
				
			// Get multiple-choice questions
			$sql_mc = '
				SELECT 
					a.quiz_question_id, 
					a.question_title, 
					a.point_worth, 
					b.quiz_answer_id, 
					b.message_on_correct, 
					b.message_on_incorrect, 
					(SELECT GROUP_CONCAT(CONCAT(CAST(c.quiz_answer_id AS CHAR(10)), ":::", c.answer_description) SEPARATOR "|||") FROM quiz_answers c WHERE c.quiz_question_id = a.quiz_question_id) AS answer_choices
				FROM quiz_questions a 
				INNER JOIN multiple_choice b ON a.quiz_question_id = b.quiz_question_id 
				WHERE a.quiz_id = :id';
			
			// Get fill-in-the-blank questions
			$sql_fib = '
				SELECT 
					a.quiz_question_id, 
					a.question_title, 
					a.point_worth, 
					b.answer 
				FROM quiz_questions a 
				INNER JOIN fill_in_blank b ON a.quiz_question_id = b.quiz_question_id 
				WHERE a.quiz_id = :id';
					
			// Get short-answer questions
			$sql_sa = '
				SELECT 
					a.quiz_question_id, 
					a.question_title, 
					a.point_worth, 
					b.explanation 
				FROM quiz_questions a 
				INNER JOIN short_answer b ON a.quiz_question_id = b.quiz_question_id 
				WHERE a.quiz_id = :id';
				
				
			// Prepare queries
			$rs_multiplechoice = $this->conn->prepare($sql_mc);
			$rs_fillinblank = $this->conn->prepare($sql_fib);
			$rs_shortanswer = $this->conn->prepare($sql_sa);
			
			// Bind parameters
			$rs_multiplechoice->bindParam(':id', $qid, PDO::PARAM_INT);
			$rs_fillinblank->bindParam(':id', $qid, PDO::PARAM_INT);
			$rs_shortanswer->bindParam(':id', $qid, PDO::PARAM_INT);
			
			// Execute queries
			$this->conn->beginTransaction();
			$rs_multiplechoice->execute();
			$rs_fillinblank->execute();
			$rs_shortanswer->execute();
			$this->conn->commit();
			
			
			// Iterate multiple-choice result set
			while($row = $rs_multiplechoice->fetch(PDO::FETCH_ASSOC))
			{
				$q = new MultipleChoice($row['quiz_question_id']);
				$q->setTitle($row['question_title']);
				$q->setPoints($row['point_worth']);
				$q->setAnswerId($row['quiz_answer_id']);
				$q->setMessageOnCorrect($row['message_on_correct']);
				$q->setMessageOnIncorrect($row['message_on_incorrect']);
				
				$choice_array = explode('|||', $row['answer_choices']);
				$choices = array();
				foreach($choice_array as $choice)
				{
					$choicesplit = explode(':::', $choice);
					$choices[$choicesplit[0]] = $choicesplit[1];
				}
				$q->setAnswerChoices($choices);
				
				$quiz->addQuestion($q);
			}
			
			
			// Iterate fill-in-blank result set
			while($row = $rs_fillinblank->fetch(PDO::FETCH_ASSOC))
			{
				$q = new FillInBlank($row['quiz_question_id']);
				$q->setTitle($row['question_title']);
				$q->setPoints($row['point_worth']);
				$q->setAnswer($row['answer']);
				
				$quiz->addQuestion($q);
			}
			
			
			// Iterate short-answer result set
			while($row = $rs_shortanswer->fetch(PDO::FETCH_ASSOC))
			{
				$q = new ShortAnswer($row['quiz_question_id']);
				$q->setTitle($row['question_title']);
				$q->setPoints($row['point_worth']);
				$q->setExplanation($row['explanation']);
				
				$quiz->addQuestion($q);
			}
		}
	}
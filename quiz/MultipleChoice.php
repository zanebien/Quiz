<?php
	class MultipleChoice extends QuizQuestion
	{
		private $answer_id;
		private $answer_choices = array();
		private $messageOnCorrect;
		private $messageOnIncorrect;
		
		public function setAnswerId($id)
		{
			$this->answer_id = $id;
		}
		
		public function setMessageOnCorrect($msg)
		{
			$this->messageOnCorrect = $msg;
		}
		
		public function setMessageOnIncorrect($msg)
		{
			$this->messageOnIncorrect = $msg;
		}
		
		public function setAnswerChoices($choices)
		{
			if(is_array($choices))
				$this->answer_choices = $choices;
			else
				throw new Exception('Choices must be an array.');
		}
		
		public function display()
		{
			$this->displayHeading();
			
			echo '<ul class="quiz_answerchoice_list">';
			foreach($this->answer_choices as $ans_id => $ans_txt)
			{
				echo '
					<li>
						<input type="radio" class="quiz_answerchoice_input" name="qid' . $this->id . '" value="aid' . $ans_id . '" id="aid' . $ans_id . '" />
						<label class="quiz_answerchoice_label" for="aid' . $ans_id . '">' . $ans_txt . '</label>
					</li>';
			}
			echo '</ul>';
			
			$this->displayFooter();
		}
	}
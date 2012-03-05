<?php
	class FillInBlank extends QuizQuestion
	{
		private $answer;
		
		public function setAnswer($ans)
		{
			$this->answer = $ans;
		}
		
		public function display()
		{
			$this->displayHeading();
			
			echo '<input type="text" class="quiz_fillinblank_box" name="qid' . $this->id . '" />';
			
			$this->displayFooter();
		}
	}
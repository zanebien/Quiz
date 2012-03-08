<?php

class ShortAnswer extends QuizQuestion
{
	private $explanation;
	
	public function setExplanation($exp)
	{
		$this->explanation = $exp;
	}
	
	public function display()
	{
		$this->displayHeading();
		
		echo '<textarea class="quiz_shortanswer_box" name="qid' . $this->id . '"></textarea>';
		
		$this->displayFooter();
	}
}
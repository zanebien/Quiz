<?php
	class Quiz
	{
		protected $quiz_id;
		protected $quiz_name;
		protected $quiz_description;
		protected $questions = array();
		
		public function __construct($id = null)
		{
			if(!is_null($id))
				$this->quiz_id = $id;
		}
		
		public function setName($name)
		{
			$this->quiz_name = $name;
		}
		
		public function setDescription($desc)
		{
			$this->quiz_description = $desc;
		}
		
		public function id()
		{
			return $this->quiz_id;
		}
		
		public function questions()
		{
			return $this->questions;
		}
		
		public function addQuestion(QuizQuestion $q)
		{
			array_push($this->questions, $q);
		}
		
		public function render()
		{
			echo '<ul class="quiz_question_list">';
			foreach($this->questions as $q)
				$q->display();
			echo '
				<li>
					<input type="submit" name="quiz_submit" value="Submit Quiz" />
				</li>';
			echo '</ul>';
		}
		
		public function randomize()
		{
			shuffle($this->questions);
		}
	}
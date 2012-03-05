<?php
	abstract class QuizQuestion
	{	
		protected $id;
		protected $title;
		protected $points;
		
		public function __construct($id = null)
		{
			if(!is_null($id))
				$this->id = $id;
		}
		
		public function setTitle($title)
		{
			$this->title = $title;
		}
		
		public function setPoints($pts)
		{
			$this->points = $pts;
		}
		
		public function getId()
		{
			return $this->id;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		public function getPoints()
		{
			return $this->points;
		}
		
		protected function displayHeading()
		{
			echo '
				<li>
					<h3 class="quiz_questiontitle">' . $this->title . '</h3>
					<p class="quiz_pointworth">Points: ' . $this->points . '</p>';
		}
		
		protected function displayFooter()
		{
			echo '</li>';
		}
		
		abstract public function display();
	}
<?php
	define('QANTITY_PARAGRAPH_NUMBERS', 4);

	define('FIRST_PARAGRAPHS_COUNT', 5);
	
	function controller()
	{
		
		$paragraphs = isset($_GET['parag'])?intval($_GET['parag']):1;
	
		$paragraphs = checkParagraphNumbers(FIRST_PARAGRAPHS_COUNT, QANTITY_PARAGRAPH_NUMBERS, $paragraphs);
		
		$page = isset($_GET['page'])?intval($_GET['page']):1;
		
		$pageData = model($page, $lastPage, $paragraphs);
		
		view($pageData, $page, $lastPage, $paragraphs);
		
	}
	
	function model(int $page, &$lastPage, int $paragraphs): array
	{
		
		$messagesArray = readData();
		
		$lastPage = countLastPage($messagesArray, $paragraphs);
		
		if (!checkPageNumber($page, $lastPage))
			die('Страница ненайдена!');
		
		return extractPageData($messagesArray, $page, $paragraphs);
		
	}
	
	function readData(): array  
	{
		
		require 'text.php';
			
		return explode("\r\n", $text);	
		
	}
	
	function countLastPage(array $messagesArray, int $paragraphs): int
	{
		
		return ceil( sizeof($messagesArray) / $paragraphs );
		
	}	
	
	function checkPageNumber(int $page, int $lastPage): bool
	{
		
		return ($page >= 1 && $page <= $lastPage);	
		
	}
	
	function extractPageData(array $messagesArray, int $page, int $paragraphs): array
	{
		
		$first = ($page - 1) * $paragraphs;
		
		return array_slice($messagesArray, $first, $paragraphs);
		
	}
	
	function view(array $pageData, int $page, int $lastPage, int $paragraphs): void
	{
		
		renderPageNumbers(FIRST_PARAGRAPHS_COUNT, QANTITY_PARAGRAPH_NUMBERS, $paragraphs );
		
		renderData($pageData);
		
		renderPagination($page, $lastPage, $paragraphs);
		
	}
	
	function renderData(array $pageData): void
	{   
		
		foreach($pageData as $messages)
		{
			
			$quantityWords = countWordsLetters($messages);
			
			$messages = markFirstLetter($messages);
			
			$messages = setColor($messages);
			
			echo "<p>$messages</p>$quantityWords";
			
		}
		
	}
	
	function renderPagination(int $page, int $lastPage, int $paragraphs): void
	{
		
		for($i = 1; $i <= $lastPage; $i++)
			if($i != $page)
				echo "<a href=\"ksr2a.php?page=$i&parag=$paragraphs\"> $i&nbsp; </a>";
			else
				echo " $i ";
		
	}
	
	function checkParagraphNumbers(int $firstParagrapsCount, int $quantity ,int $paragraps): int
	{
		
		$maxParagraph = calculateMaxParagraphs($firstParagrapsCount, $quantity);
	
		if($paragraps <= $firstParagrapsCount)
			$paragraps = $firstParagrapsCount;
		else
		{
			if($paragraps > $maxParagraph)
				$paragraps = $maxParagraph;
			else
				for ($i = $firstParagrapsCount; $i < $maxParagraph; $i *= 2)
				{
		
					if($paragraps > $i && $paragraps <= $i*2)
						$paragraps = $i*2;
				}
		}
	
		return $paragraps;
		
	}
	
	function calculateMaxParagraphs(int $firstParagrapsCount, int $quantity): int
	{
		
		return $firstParagrapsCount * pow(2,($quantity-1));
		
	}

	function renderPageNumbers(int $firstParagrapsCount, int $quantity, int $paragraps )
	{
		
		$maxParagraph = calculateMaxParagraphs($firstParagrapsCount, $quantity);
	
		echo 'Колличество абзацев: '; 
	
		for ($i = $firstParagrapsCount; $i <= $maxParagraph; $i *= 2)
		{
			if ($i!=$paragraps)
				echo "<a href=\"ksr2a.php?parag=$i\"> $i&nbsp; </a> ";
			else
				echo ' ',$i, ' ';
		
			if ($i < $maxParagraph)
				echo '|';
			else
				echo '</br>';
		}
	
	}
	
	function countWordsLetters(string $dataString): string
	{
		
		$regexp = "/[А-Яа-яЁёa-z]+[\-]{0,1}[А-Яа-яЁёa-z]*/i"; 
	
		$dataString = strip_tags($dataString);
		
		$amountWords = preg_match_all($regexp, $dataString, $out, PREG_PATTERN_ORDER);
		
		$amountLetters = iconv_strlen($dataString,'windows-1251'); 
		
		return "Колличество слов: $amountWords<br>Общая длина абзаца: $amountLetters<br>";
		
	}
	
	function markFirstLetter(string $dataString): string
	{
	
		$regexp = "/(^|[?!.]\s*)([А-Яа-яЁёa-z])/i";  
		
		$replacement = "$1<b>$2</b>";
		
		return preg_replace($regexp, $replacement, $dataString);
		
	}
	
    function makeRandomColor(): string
	{
		
		$colors = ["brown","green","blue","red"];
		
		$index = mt_rand(0, (sizeof($colors)-1));
		
		return $colors[$index];
		
	}
	
	function setColor(string $dataString): string
	{
	
	$regexp = '/[\<a-z\>]*([HPJ])[\<\/a-z\>]*(TML|HP|SP.NET|SP|ava)/i';  
		
		$color = makeRandomColor();
		
		$replacement ="<span style=\"color: $color;\">$1$2</span>";
		
		//$replacement ="<b>$1</b>";
 
		return preg_replace($regexp, $replacement, $dataString);
		
	}
?>
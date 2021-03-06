<?php
  session_start();
  require_once('connection.php');
  require_once('middleware.php');
  if(!isset($_SESSION['question_feeded']))
  header('location: admin_logout.php');
  $errors = array();
  if (isset($_POST['question']) && isset($_POST['option1']) && isset($_POST['option2']) && isset($_POST['option3']) && isset($_POST['option4']) && isset($_POST['answer']) && isset($_POST['reason']) && isset($_POST['formatted'])) {
      $question = $_POST['question'];
      $option1  = $_POST['option1'];
      $option2  = $_POST['option2'];
      $option3  = $_POST['option3'];
      $option4  = $_POST['option4'];
      $answer   = $_POST['answer'];
      $reason   = $_POST['reason'];
      $formatted = $_POST['formatted'];
      $num      = $_SESSION['question_num'];
      $quiz_id  = $_SESSION['quiz_id'];
      $error_control = 0;
      $questions_inserted = 0;
      for ($i=0;$i<$num;$i++) {
          $que = convertEntities($question[$i]);
          $o1 = convertEntities($option1[$i]);
          $o2 = convertEntities($option2[$i]);
          $o3 = convertEntities($option3[$i]);
          $o4 = convertEntities($option4[$i]);
          $res = convertEntities($reason[$i]);
          $form = convertEntities($formatted[$i]);
          $ans = $answer[$i];
          $break = '<br/>';
          $control = 1;
          if($que == ''){
              $err = 'Q '.($i+1).' discarded, Empty question entry not allowed';
              $control = 0;
              $error_control = 1;
              array_push($errors, $err.$break);
          }
          else if($o1=='' || $o2=='' || $o3=='' || $o4 == ''){
              $err = 'Q '.($i+1).' discarded, Options can not be empty';
              $control = 0;
              $error_control = 1;
              array_push($errors, $err.$break);
          }
          else if($ans!=1 && $ans!=2 && $ans!=3 && $ans!=4){
              $err = 'Q '.($i+1).' discarded, Invalid answer value';
              $control = 0;
              $error_control = 1;
              array_push($errors, $err.$break);
          }
          else if($form!=0 && $form!=1){
            $err = 'Q '.($i+1).' discarded, Invalid formatted option';
            $control = 0;
            $error_control = 1;
            array_push($errors, $err.$break);
          }
          if($control){
              $questions_inserted++;
            $sql = "INSERT INTO `question_bank` (`question_id`, `quiz_id`, `question`, `option_1`, `option_2`, `option_3`, `option_4`, `answer`, `reason`, `formatted`) VALUES (NULL, '$quiz_id', '$que', '$o1', '$o2', '$o3', '$o4', '$ans', '$res', '$form')";
            mysqli_query($conn, $sql);
          }
      }

      if($questions_inserted){
          if($questions_inserted == 1)
            $msg = 'Question';
          else $msg = 'Questions'; 
          $_SESSION['success_msg'] = $questions_inserted.' '.$msg.' added to quiz successfully';
      }

      if($error_control){
          
          foreach($errors as $err){
              $_SESSION['error_msg'] .= $err;
          }
      }

      unset($_SESSION['question_details_set']);
      unset($_SESSION['question_num']);
      unset($_SESSION['quiz_name']);
      unset($_SESSION['quiz_id']);
      unset($_SESSION['question_feeded']);
      if($questions_inserted){
        $_SESSION['message'] = 'Questions Added Successfully';
        $_SESSION['color'] = '#68a030';
      }
      else{
        $_SESSION['message'] = 'No questions were added';
        $_SESSION['color'] = '#cd201f';
      }
      header('location: admin_dashboard.php');
  }
  else{
      $_SESSION['message'] = 'Something went wrong!';
      $_SESSION['color'] = '#cd201f';
      unset($_SESSION['question_feeded']);
      header('location: admin_dashboard.php');
  }

?>
<?php
/**
 * Elgg Poll plugin
 * @package Elggpoll
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @Original author John Mellberg
 * website http://www.syslogicinc.com
 * @Modified By Team Webgalli to work with ElggV1.5
 * www.webgalli.com or www.m4medicine.com
 */


if (isset($vars['entity'])) {

	//set img src
	$img_src = elgg_get_site_url() . "mod/polls/graphics/poll.gif";

	$question = $vars['entity']->question;

	//get the array of possible responses
	$responses = polls_get_choice_array($vars['entity']);

	//get the array of user responses to the poll
	$user_responses = $vars['entity']->getAnnotations('vote',9999,0,'desc');
	//get the count of responses
	$user_responses_count = $vars['entity']->countAnnotations('vote');

	//create new array to store response and count
	//$response_count = array();

        $open_poll = ($vars['entity']->open_poll == 1);

	//populate array
        $vote_id=0;
	foreach($responses as $response)
	{
                $vote_id++;

		//get count per response
		$response_count = polls_get_response_count($response, $user_responses);
                $response_annotations = elgg_get_annotations(array( 'guid'=>$vars['entity']->guid,
                                                                    'annotation_name' => 'vote',
                                                                    'annotation_value' => $response));
                $voted_users = '';
                // show voted users if poll is open or current_user is admin
                if($open_poll or elgg_is_admin_logged_in()) {
                    // css hide when admin are watching secret ballot, can manualy open users
                    // list later clicking on label
                    $display_style = $open_poll ? '1' : 'style="display:none;"';

                    $user_guids = array();
                    foreach($response_annotations as $ur) { $user_guids[] = $ur->owner_guid; }
                    if (!empty($user_guids)) {
                        // form voted users' icons list div
                        $voted_users = '<div class="polls-users-voted" '.$display_style.' id="polls-users-vote-'.$vote_id.'">';
                        $voted_users .= elgg_list_entities(array("guids"=>$user_guids,
                                                            "full_view" => false,
                                                            "pagination" => false,
                                                            "list_type" => "users",
                                                            "gallery_class" => "elgg-gallery-users",
                                                            "size" => "small"));
                        $voted_users .= '</div>';
                    }
                }
			
		//calculate %
		if ($response_count && $user_responses_count) {
			$response_percentage = round(100 / ($user_responses_count / $response_count));
		} else {
			$response_percentage = 0;
		}
			
		//html
		?>
<div class="progress_indicator">
        <label title='Show votes' class='polls-vote-label' onClick='$("#polls-users-vote-<?php echo $vote_id; ?>").toggle();'><?php echo $response . " (" . $response_count . ")"; ?> </label><br>
	<div class="progressBarContainer" align="left">
		<div class="polls-filled-bar"
			style="width: <?php echo $response_percentage; ?>%"></div>
	</div>
</div>
<?php echo $voted_users; ?>
<br>
		<?php
	}
	?>

<p>
<?php echo elgg_echo('polls:totalvotes') . $user_responses_count; ?>
</p>

<?php

}
else
{
	register_error(elgg_echo("polls:blank"));
	forward("mod/polls/all");
}

<?php
/**
 * Register all forms
 *
 * Maintain a list of all hooks that are registered throughout
 * the theme, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory_Theme_Forms/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Directory_Theme_Forms {
	/**
	 * The ID of this Theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $theme_name    The ID of this theme.
	 */
	private $theme_name;

	/**
	 * The version of this theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this theme.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $theme_name	The name of the theme.
	 * @param      string    $version    The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {
		$this->theme_name = $theme_name;
		$this->version = $version;
		$this->run();
	}
	/**
	 * Start required functions
	 */
	public function run(){
		add_shortcode( 'survey', array($this, 'pi_survey' ) );
	}

	public function register_pi_forms_menu_page(){
	    add_menu_page( 
	    	'Forms Report', 
	    	'Forms', 
	    	'manage_options', 
	    	'pi_forms_menu', 
	    	array($this, 'pi_forms_menu_page'), 
	    	'', 
	    	6 
	    );
	}
	public function pi_forms_menu_page(){
		/*Get posts with post type ac_forms*/
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'pi_form',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts($args);
		/*Start count at 1*/
		$count = 1;
		echo '<div class="pi-admin-show wrap">';
			echo '<h1 class="widefat">Form Reports: </h1><hr style="margin-bottom: 40px;">';
			echo '<p>View Form Summary Reports</p>';
			/*Present the data*/
			foreach ($posts as $post) {
				$post_meta = get_post_meta( $post->ID );
				echo '<h3>Submission ' . $count . ':</h3>'; 
					echo '<ul class="pi-data-show">';
						foreach ($post_meta as $field => $value) {
							echo '<li>'. $field .': '. $value[0] .'</li>';
						}
					echo '</ul>';
				echo '<hr>';
				/*Increment the count only within this loop*/
				$count ++;
			}
		echo '</div>';
	}
	public function pi_form_drug_assesment_wrapper(){
		ob_start();
		?>	
		<div class="modal fade pi-modal" id="pi-questionnaire-drug" tabindex="-1" role="dialog" aria-labelledby="pi-modal-label" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="acModalLabel"><strong>Am I Addicted to Drugs?</strong> - Questionnaire</h4>
					</div>
					<div class="modal-body">
						<?= $this->drug_questionnaire(); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-error" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;		
	}
	public function drug_questionnaire(){
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';	
		$content .= '<div id="ac-tabs">';
			$content .= '<ul id="testi-tabs" class="nav nav-tabs">
	                        <li class="tab-btn active" id="tab-btn-' . 1 . '" ><a href="#tabs-1">Step 1</a></li>
	                        <li class="tab-btn" id="tab-btn-' . 2 . '" ><a href="#tabs-2">Step 2</a></li>
	                        <li class="tab-btn" id="tab-btn-' . 3 . '" ><a href="#tabs-3">Step 3</a></li>
	                        <li class="tab-btn" id="tab-btn-' . 4 . '" ><a href="#tabs-4">Step 4</a></li>
	                        <li class="tab-btn" id="tab-btn-' . 5 . '" ><a href="#tabs-5">Step 5</a></li>
	                        <li class="tab-btn" id="tab-btn-' . 6 . '" ><a href="#tabs-6">Your Results</a></li>
	                    </ul>';
	        $content .= '<div id="tabs-1" class="ac-tab active" data-number="1">';
	        	$content .= '<fieldset class="form-group">';
		        	$content .= '<label for="pi_name">Name: </label>';            
					$content .= '<input type="text" name="pi_name" class="required">';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="pi_phone">Phone Number: </label>'; 
					$content .= '<input type="text" name="pi_phone" class="required">';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="pi_email">Email: </label>'; 
					$content .= '<input type="email" name="pi_email"  class="required">';
				$content .= '</fieldset>';
				$content .= '<a class="btn btn-primary first-next">Next</a>';
			$content .= '</div>';
			$content .= '<div id="tabs-2" class="inactive ac-tab" data-number="2">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="ilicit_drugs">1. Have you ever used illicit drugs?</label>';            
					$content .= '<input type="radio" name="ilicit_drugs" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="ilicit_drugs" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="presc_meds">2. Have you used prescription medications in a way that was not originally intended by a doctor?</label>';            
					$content .= '<input type="radio" name="presc_meds" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="presc_meds" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="more_one">3. Have you abused more than one drug at a time?</label>';            
					$content .= '<input type="radio" name="more_one" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="more_one" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="week_wo">4. Is it difficult to get through the week without abusing drugs?</label>';            
					$content .= '<input type="radio" name="week_wo" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="week_wo" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="willpower">5. Do you have the willpower to stop using drugs at any given time?</label>';            
					$content .= '<input type="radio" name="willpower" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="willpower" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary ac-previous">Previous</a>';
					$content .= '<a class="btn btn-primary ac-next">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			$content .= '<div id="tabs-3" class="inactive ac-tab" data-number="3">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="blackouts">6. Has your drug use resulted in blackouts?</label>';            
					$content .= '<input type="radio" name="blackouts" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="blackouts" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="guilty">7. Do your drug habits make you feel guilty?</label>';            
					$content .= '<input type="radio" name="guilty" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="guilty" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="loved_ones">8. Have your loved ones ever expressed their concerns about your drug habits?</label>';            
					$content .= '<input type="radio" name="loved_ones" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="loved_ones" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="relationships">9. Has your drug use negatively affected your relationships with others?</label>';            
					$content .= '<input type="radio" name="relationships" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="relationships" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="lose_friends">10. Has drug use ever caused you to lose friends?</label>';            
					$content .= '<input type="radio" name="lose_friends" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="lose_friends" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary ac-previous">Previous</a>';
					$content .= '<a class="btn btn-primary ac-next">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			$content .= '<div id="tabs-4" class="inactive ac-tab" data-number="4">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="neglect_family">11. Has drug use ever led you to neglect your family?</label>';            
					$content .= '<input type="radio" name="neglect_family" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="neglect_family" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="trouble">12. Have your drug habits gotten you into trouble at work?</label>';            
					$content .= '<input type="radio" name="trouble" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="trouble" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="lose_job">13. Has drug use caused you to lose a job?</label>';            
					$content .= '<input type="radio" name="lose_job" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="lose_job" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="fight">14. Has being under the influence of drugs ever initiated a fight?</label>';            
					$content .= '<input type="radio" name="fight" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="fight" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="illegal_activities">15. Have you ever obtained drugs through illegal activities?</label>';            
					$content .= '<input type="radio" name="illegal_activities" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="illegal_activities" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary ac-previous">Previous</a>';
					$content .= '<a class="btn btn-primary ac-next" >Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			$content .= '<div id="tabs-5" class="inactive ac-tab" data-number="5">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="legal_problems">16. Has your drug use ever been associated with legal problems?</label>';            
					$content .= '<input type="radio" name="legal_problems" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="legal_problems" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="withdrawal_symp">17. When you have ceased using drugs, have you experienced symptoms of withdrawal?</label>';            
					$content .= '<input type="radio" name="withdrawal_symp" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="withdrawal_symp" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="med_issues">18. Have medical issues ever resulted from your drug use (bleeding, memory loss, hepatitis, seizures)?</label>';            
					$content .= '<input type="radio" name="med_issues" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="med_issues" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="seeking_treatment">19. Have you thought about seeking treatment for drug abuse?</label>';            
					$content .= '<input type="radio" name="seeking_treatment" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="seeking_treatment" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="treatment_before">20. Have you been involved in a drug abuse treatment program before?</label>';            
					$content .= '<input type="radio" name="treatment_before" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="treatment_before" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary ac-previous">Previous</a>';
					$content .= '<a class="btn btn-primary" id="self-assesment">Get Final Score</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			$content .= '<div id="tabs-6" class="inactive ac-tab" data-number="6">';
				$content .= '<span class="ac-tab-msg"></span>';
				$content .= '<p class="ac-tab-total">You’ve Answered <strong>“YES”</strong> to <strong id="ac-tab-total"></strong> out of <strong>20</strong> Questions.</p>';
				$content .= '<p class="ac-tab-final"></p>';
			$content .= '</div>';
		$content .= '</div></form>';
		return $content; 		
	}
	public function pi_form_alcohol_assesment_wrapper(){
		ob_start();
		?>
		<div class="modal fade pi-modal" id="pi-questionnaire-alcohol" tabindex="-1" role="dialog" aria-labelledby="pi-modal-label" aria-hidden="true" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="acModalLabel"><strong>Am I Addicted to Alcohol?</strong> - Questionnaire</h4>
					</div>
					<div class="modal-body">
						<?= $this->alcohol_questionnaire(); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-error" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;		
	}
	public function alcohol_questionnaire(){
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
		$content .= '<div id="aa-tabs">';
			$content .= '<ul class="nav nav-tabs">
	                        <li class="tab-btn active" id="aa-tab-btn-' . 1 . '" ><a href="#aa-tabs-1">Step 1</a></li>
	                        <li class="tab-btn" id="aa-tab-btn-' . 2 . '" ><a href="#aa-tabs-2">Step 2</a></li>
	                        <li class="tab-btn" id="aa-tab-btn-' . 3 . '" ><a href="#aa-tabs-3">Step 3</a></li>
	                        <li class="tab-btn" id="aa-tab-btn-' . 4 . '" ><a href="#aa-tabs-4">Step 4</a></li>
	                        <li class="tab-btn" id="aa-tab-btn-' . 5 . '" ><a href="#aa-tabs-5">Step 5</a></li>
	                        <li class="tab-btn" id="aa-tab-btn-' . 6 . '" ><a href="#aa-tabs-6">Your Results</a></li>
	                    </ul>';
	        $content .= '<div id="aa-tabs-1" class="ac-tab active" data-number="1">';
	        	$content .= '<fieldset class="form-group">';
		        	$content .= '<label for="pi_name">Name: </label>';            
					$content .= '<input type="text" name="pi_name" class="required">';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="pi_phone">Phone Number: </label>'; 
					$content .= '<input type="text" name="pi_phone" class="required">';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="pi_email">Email: </label>'; 
					$content .= '<input type="email" name="pi_email"  class="required">';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary first-next">Next</a>';
				$contnet .= '</fieldset>';
			$content .= '</div>';
			//tab 2
			$content .= '<div id="aa-tabs-2" class="inactive ac-tab" data-number="2">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="drink">1. On average, do you feel that you drink the same or less than most people?</label>';            
					$content .= '<input type="radio" name="drink" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="drink" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="blackout">2. Has heavy drinking caused you to experience a blackout?</label>';            
					$content .= '<input type="radio" name="blackout" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="blackout" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="oved_drink">3. Has a loved one ever expressed concern about your drinking?</label>';            
					$content .= '<input type="radio" name="oved_drink" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="oved_drink" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="stop_drinking">4. Is it difficult for you to stop drinking once you have begun?</label>';            
					$content .= '<input type="radio" name="stop_drinking" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="stop_drinking" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="guilty">5. Does drinking ever make you feel guilty?</label>';            
					$content .= '<input type="radio" name="guilty" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="guilty" value="no"> No';
				$contnet .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary aa-previous">Previous</a>';
					$content .= '<a class="btn btn-primary aa-next">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			//tab 3
			$content .= '<div id="aa-tabs-3" class="inactive ac-tab" data-number="3">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="aa">6. Have you considered attending an Alcoholics Anonymous meeting?</label>';            
					$content .= '<input type="radio" name="aa" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="aa" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="altercation">7. Has drinking ever caused you to get into a physical altercation?</label>';            
					$content .= '<input type="radio" name="altercation" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="altercation" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="neg_relation">8. Has alcohol negatively affected your relationships?</label>';            
					$content .= '<input type="radio" name="neg_relation" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="neg_relation" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="intervene">9. Has your family or close friends ever tried to intervene when you are drinking?</label>';            
					$content .= '<input type="radio" name="intervene" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="intervene" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="lose_friends">10. Have you ever lost friendships due to your drinking?</label>';            
					$content .= '<input type="radio" name="lose_friends" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="lose_friends" value="no"> No';
				$contnet .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary aa-previous">Previous</a>';
					$content .= '<a class="btn btn-primary aa-next">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			//tab 4
			$content .= '<div id="aa-tabs-4" class="inactive ac-tab" data-number="4">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="trouble_work">11. Has drinking ever gotten you into trouble at work?</label>';            
					$content .= '<input type="radio" name="trouble_work" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="trouble_work" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="loose_job">12. Has drinking ever caused you to lose a job?</label>';            
					$content .= '<input type="radio" name="loose_job" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="loose_job" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="reponsabilities">13. Does drinking cause you to neglect responsibilities you once found important?</label>';            
					$content .= '<input type="radio" name="reponsabilities" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="reponsabilities" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="noon">14. Would you consider yourself to drink before noon relatively often?</label>';            
					$content .= '<input type="radio" name="noon" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="noon" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="cirrhosis">15. Have you experienced any liver problems, such as cirrhosis?</label>';            
					$content .= '<input type="radio" name="cirrhosis" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="cirrhosis" value="no"> No';
				$contnet .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary aa-previous">Previous</a>';
					$content .= '<a class="btn btn-primary aa-next">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			//tab5
			$content .= '<div id="aa-tabs-5" class="inactive ac-tab" data-number="5">';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="help">16. Have you ever thought about getting help for your drinking habits?</label>';            
					$content .= '<input type="radio" name="help" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="help" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="hallucinations">17. Have you experienced severe shaking or visual/auditory hallucinations after heavy drinking?</label>';            
					$content .= '<input type="radio" name="hallucinations" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="hallucinations" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="injury">18. Has drinking ever ended in mental or physical injury?</label>';            
					$content .= '<input type="radio" name="injury" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="injury" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="dui">19. Have you found yourself driving under the influence of alcohol?</label>';            
					$content .= '<input type="radio" name="dui" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="dui" value="no"> No';
				$content .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<label for="legal">20. Has drinking caused you to get into legal trouble? </label>';            
					$content .= '<input type="radio" name="legal" value="Yes" checked> Yes';
					$content .= '<input type="radio" name="legal" value="no"> No';
				$contnet .= '</fieldset>';
				$content .= '<fieldset class="form-group">';
					$content .= '<a class="btn btn-primary aa-previous">Previous</a>';
					$content .= '<a class="btn btn-primary aa-next" id="aa-self-ass">Next</a>';
				$content .= '</fieldset>';
			$content .= '</div>';
			//final tab
			$content .= '<div id="aa-tabs-6" class="inactive ac-tab" data-number="6">';
				$content .= '<span class="ac-tab-msg"></span>';
				$content .= '<p class="ac-tab-total">You’ve Answered <strong>“YES”</strong> to <strong id="aa-tab-total"></strong> out of <strong>20</strong> Questions.</p>';
				$content .= '<p class="ac-tab-final"></p>';
			$content .= '</div>';
		$content .= '</div></form>';
		return $content; 		
	}
	/*Submit Form Helper methods*/
	public function pi_set_content_type(){
    	return "text/html";
	}
	public function find_users_ip(){
		// /*Get ip information from user*/
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	public function update_pi_form_fost( $posted, $ip, $current_time ){
			extract($posted);
			$pi_post = array(
				'post_title'    => $pi_name . 'contact form',
				'post_content'  => $pi_message,
				'post_status'   => 'publish',
				'post_type'		=> 'pi_form'
			);
			$url = home_url();
			/*Insert the post while getting the id*/
			$post_id =  wp_insert_post( $pi_post );
			add_post_meta( $post_id, 'pi_name', $pi_name, true);
			add_post_meta( $post_id, 'pi_phone', $pi_phone, true);
			add_post_meta( $post_id, 'pi_email', $pi_email, true);
			add_post_meta( $post_id, 'pi_select', $pi_select, true);
			add_post_meta( $post_id, 'pi_choice', $pi_choice, true);
			add_post_meta( $post_id, 'pi_time', $pi_time, true);
			add_post_meta( $post_id, 'pi_questions', $pi_message, true);
			add_post_meta( $post_id, 'ip', $ip, true);
			add_post_meta( $post_id, 'current_time', $current_time, true);
			add_post_meta( $post_id, 'url', $url, true);
	}
	public function send_to_form_system( $posted, $ip, $current_time){
		$username = 'Bh2P64xc30Ojq51NaXBvgWzDpzrqkHyd';
		$password = 'goleador7';
		$pi_url = 'http://formresults123.com/v1/forms';
		$method = 'POST';
		$url = home_url();
		extract($posted);
		$data = array(
			'name'		=> $pi_name, 
			'phone'		=> $pi_phone, 
			'email'		=> $pi_email, 
			'person' 	=> $pi_select,
			'drug'		=> $pi_choice, 
			'time'		=> $pi_time, 
			'comment'	=> $pi_message, 
			'ip' 		=> $ip, 
			'url' 		=> $url, 
			'sent'		=> $current_time 	
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $pi_url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
		curl_setopt($ch, CURLOPT_USERPWD,"$username:$password"); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);		
	}
	public function validate( $posted, $total){
			if ( count($posted ) > 1){
				foreach ($posted as $key => $value) {
					switch ( $key ) {
						case 'pi_name':
							if( !$value ) {
							  $errors[] = "Please Enter your Name";
							}
							break;
						case 'pi_phone':
							if( !$value ) {
								$errors[] = "Please Enter your Phone Number";
							}elseif( !ctype_digit( $value ) ){
								$errors[] = "Please Enter a Valid Phone Number";
							}
							break;
						case 'pi_email':
							if( !$value ) {
							  $errors[] = "Please Enter your Email";
							} elseif( !filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
							  $errors[] = "Please Enter a Valid Email";
							}
							break;

						case 'total':
							if( !$value){
								$errors[] = "Please Enter an Answer";
							}elseif( $total != $value ){
								$errors[] = "Please Enter the Right Answer";							
							}
							break;
						
						default:
							# code...
							break;
					}			
				}
			}else{
				foreach ($posted as $key => $value) {
					switch ( $key ) {
						case 'pi_name':
							if( !$value ) {
							  $errors = "Please enter your Name";
							}
							break;
						case 'pi_phone':
							if( !$value ) {
								$errors = "Please enter your Phone Number";
							}elseif( !ctype_digit( $value ) ){
								$errors = "Please enter a Valid Phone Number";
							}
							break;
						case 'pi_email':
							if( !$value ) {
							  $errors = "Please enter your Email";
							} elseif( !filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
							  $errors = "Please enter a Valid Email";
							}
							break;
						case 'total':
							if( !$value){
								$errors[] = "Please Enter an Answer";
							}elseif( $total != $value ){
								$errors[] = "Please Enter the Right Answer";							
							}
							break;
						
						default:
							# code...
							break;
					}			
				}			
			}
		return $errors;		
	}
	public function pi_get_message( $posted ){
		$message  = 'Url: ' . home_url() . '<br>';
		foreach ($posted as $key => $value) {
			switch ( $key ) {
				case 'pi_name':
					$message .= 'Name: ' . $value . '<br>';
					break;
				case 'pi_phone':
					$message .= 'Phone: ' . $value . '<br>';
					break;
				case 'pi_email':
					$message .= 'Email: ' . $value . '<br>';
					break;
				case 'pi_select':
					$message .= 'Seeking treatment for: ' . $value . '<br>';
					break;
				case 'pi_choice':
					$message .= 'Drug of choice: ' . $value . '<br>';
					break;
				case 'pi_time':
					$message .= 'Time using it: ' . $value . '<br>';
					break;
				case 'pi_message':
					$message .= 'Message: ' . $value . '<br>';
					break;
				default:
					//nothing
					break;
			}				
		}
		return $message;	
	}
	public function validate_important( $name, $phone, $email){

		if( !$name ) {
		  $errors[] = "Please Enter your Name";
		}
		if( !$phone ) {
			$errors[] = "Please Enter your Phone Number";
		}elseif( !ctype_digit( $phone ) ){
			$errors[] = "Please Enter a Valid Phone Number";
		}
		if( !$email ) {
		  $errors[] = "Please Enter your Email";
		} elseif( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		  $errors[] = "Please Enter a Valid Email";
		}
		return $errors;

	}
	public function pi_input_ajaxhandler() {
		if ( !isset( $_POST[ 'nonce' ] ) || !wp_verify_nonce( $_POST[ 'nonce' ], 'pi_msg_ajax') ) {
			status_header( '401' );
			die('Busted!');
		}

		$total = $_POST['total'];

		$posted = array();
		parse_str( $_POST[ 'formData' ] , $posted);
		$error = $this->validate($posted, $total);
		if( $error ){
			wp_send_json_error( $error );
		}else{
			wp_send_json_success('Awesome!');
		}
	}
	public function pi_next_ajaxhandler() {
		if ( !isset( $_POST[ 'nonce' ] ) || !wp_verify_nonce( $_POST[ 'nonce' ], 'pi_msg_ajax') ) {
			status_header( '401' );
			die('Busted!');
		}
		
		$name  = $_POST['name'];
		$phone = $_POST['phone'];
		$email = $_POST['email'];
		
		$errors = $this->validate_important($name, $phone, $email);
		if( $errors ){
			wp_send_json_error( $errors );
		}else{
			wp_send_json_success('Awesome!');
		}
	}
	public function pi_form_ajaxhandler() {
		if ( !isset( $_POST[ 'nonce' ] ) || !wp_verify_nonce( $_POST[ 'nonce' ], 'pi_msg_ajax') ) {
			status_header( '401' );
			die('Busted!');
		}

		$total = $_POST['total'];

		$posted = array();
		parse_str( $_POST[ 'formData' ] , $posted);
		$errors = $this->validate($posted, $total);
		if( $errors ){
			wp_send_json_error( $errors );
		}else{
			$ip = $this->find_users_ip();
			$current_time = time();

			$to = array('aabello@recoveryhealthcaresystems.com');
			$subject = get_bloginfo('name') . ' Contact';
			$message = $this->pi_get_message( $posted );
			// $to = array('management@treatsearch.com', 'leads@treatsearch.com' , 'development@treatsearch.com');
			$headers[] = 'From: ' . get_bloginfo('name') . ' /<helpline@fordetox.com>';
			/*Send Email*/
	    	wp_mail( $to, $subject, $message, $headers );

	    	/*Create BackUp Email*/
	    	$this->update_pi_form_fost( $posted, $ip, $current_time );

	    	/*Send to form system*/
	    	$this->send_to_form_system( $posted, $ip, $current_time);

	    	/*Send success message to browser*/
			wp_send_json_success('Thank you for Contacting us. An <strong>Addiction Specialist</strong> will return your request within the next 24 hours. You can go back <a href="'. home_url() .'" >to our home page here.</a>');
		}
	}
	public function pi_questionnaire_ajaxhandler() {
		if ( !isset( $_POST[ 'nonce' ] ) || !wp_verify_nonce( $_POST[ 'nonce' ], 'pi_msg_ajax') ) {
			status_header( '401' );
			die('Busted!');
		}

		$posted = array();
		parse_str( $_POST[ 'formData' ] , $posted);

		$ip = $this->find_users_ip();
		$current_time = time();

		$to = array('aabello@recoveryhealthcaresystems.com');
		$subject = get_bloginfo('name') . ' Contact';
		$message = $this->pi_get_message( $posted );
		// $to = array('management@treatsearch.com', 'leads@treatsearch.com' , 'development@treatsearch.com');
		$headers[] = 'From:' . get_bloginfo('name') . ' /<info@drugtreatmentcentersillinois.com>';
		
		 /*Send Email*/
    	wp_mail( $to, $subject, $message, $headers );

    	/*Create BackUp Email*/
    	$this->update_pi_form_fost( $posted, $ip, $current_time );

    	/*Send to form system*/
    	$this->send_to_form_system( $posted, $ip, $current_time);

    	/*Send success message to browser*/
		wp_send_json_success( home_url() );
	}
	/**
	 * Respond to claim listing POST request
	 */
	public function pi_ajax_submit_survey() {
	    //if user is logged in validate nonce and then save their choice
		if ( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'pi_msg_ajax') ) {
			status_header( '401' );
			die();
		}else{
			$survey_type = $_POST['surveyType'];
			$posted = array();
			parse_str( $_POST[ 'formData' ] , $posted);
			
			if( $survey_type === 'alumni-survey'){
				
				$message = $this->pi_process_alumni_survey($posted);

			}elseif( $survey_type === 'staff-survey'){

				$message = $this->pi_process_staff_survey($posted);

			}elseif( $survey_type === 'family-survey'){

				$message = $this->pi_process_family_survey($posted);

			}
			// $to = array('helpline@fordetox.com', 'pbrooke@wstreatment.com' , 'newimage100@aol.com');
			$to = 'aabello@recoveryhealthcaresystems.com';
			$subject = 'Patient Survey from' . get_bloginfo('name');
			$headers[] = 'From:' . get_bloginfo('name') . ' <info@drugtreatmentcentersillinois.com>';
			wp_mail( $to, $subject, $message, $headers);

			$response = 'Thank you for submitting the survey. <a href="'.home_url().'">To continue using this website click here</a>.';
		    wp_send_json_success( $response );		
		}
	}

	public function pi_survey( $atts ) {
		$atts = shortcode_atts( array(
			'type' => 'patient',
		), $atts, 'survey' );
		$content = '<span class="alert-box"></span>';
		if( $atts['type'] === 'patient'){
			$content .= $this->pi_alumni_survey();
		}elseif( $atts['type'] === 'staff'){
			$content .= $this->pi_staff_survey();
		}elseif( $atts['type'] === 'family'){
			$content .= $this->pi_family_survey();
		}
		return $content;
	}
	public function pi_alumni_survey(){
		ob_start();
		?>
		<div class="survey-wrapper">
			<h2>Take this survey if you have attended a treatment center</h2>
			<p>
				In this survey, you are being asked to answer questions on behalf of <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?>. We provide people who are struggling with addiction with the proper resources and help they need. Your answers will help to form a complete and impartial review of the drug treatment center, which in turn, will help our users make the best decision in terms of their specific needs for care. All answers, unless otherwise requested, will be kept anonymous, and you will never be contacted by <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?> without your consent. Thank you for your contribution.
			</p>
			<form action="<?= htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>" method="post" enctype="multipart/form-data" class="pi-survey">
				<div class="form-group">
					<label for="facility-name" class="main-label">1. Name of Treatment Center or Facility</label>
					<input type="text" name="facility-name">
				</div>

				<div class="form-group multi-text">
					<label class="main-label">2. Location of the Treatment Facility</label>
					
					<label for="city" class="reg-label">City/Town </label>
					<input type="text" name="city"><br>
					
					<label for="state" class="reg-label">State/Provice </label>
					<select type="text" name="state">
						<?php foreach (get_states() as $key => $state) : ?>
							<option value="<?= $key; ?>"><?= $state; ?></option>
						<?php endforeach; ?>
					</select><br>
					
					<label for="country" class="reg-label">Country </label>
					<input type="text" name="country">
				</div>

				<div class="form-group">
					<label class="main-label">3. Date you began treatment</label>
					<input type="text" class="input-number" name="date-month" placeholder="MM"> /
					<input type="text" class="input-number" name="date-day" placeholder="DD"> /
					<input type="text" class="input-number" name="date-year" placeholder="YY">
				</div>

				<div class="form-group">
					<label class="main-label" for="led-decision">4. What led to your decision to enter treatment?</label>

					<input type="radio" name="led-decision" value="Doctor referral">
					<label class="reg-label">Doctor referral</label><br>

					<input type="radio" name="led-decision" value="Mandated by court">
					<label class="reg-label">Mandated by court</label><br>
					
					<input type="radio" name="led-decision" value="Familiy Intervention">
					<label class="reg-label">Familiy Intervention</label><br>
					
					<input type="radio" name="led-decision" value="Personal decision">
					<label class="reg-label">Personal decision </label><br>
					
					<label class="reg-label" for="other-decision">Other</label>
					<input type="text" name="other-decision">
				</div>
				
				<div class="form-group">
					<label class="main-label" for="age">5. At what age did you enter rehab?</label>
					<input type="text" class="input-number" name="age">
				</div>

				<div class="form-group">
					<label for="listing-web" class="main-label">6. How did you choose this treatment facility?</label>
					<input type="radio" name="how-choice" value="Doctor referral">
					<label class="reg-label" for="how-choice">Doctor referral</label><br>
					
					<input type="radio" name="how-choice" value="Referral from friends and family">
					<label class="reg-label">Referral from friends and family </label><br>
					
					<input type="radio" name="how-choice" value="Familiy Intervention">
					<label class="reg-label">Familiy Intervention</label><br>
					
					<input type="radio" name="how-choice" value="Personal decision">
					<label class="reg-label">Personal decision </label><br>
				
					<label class="reg-label" for="other-choice">Other</label>
					<input type="text" name="other-choice"><br>
				</div>

				<div class="form-group">
					<label for="condition" class="main-label">7. What condition were you treated for?</label>
					<input type="radio" name="condition" value="Illegal Drug Addiction">
					<label class="reg-label" for="how-choice">Alcohol addiction </label><br>

					<input type="radio" name="condition" value="Alcohol addiction">
					<label class="reg-label">Illegal Drug Addiction </label><br>

					<input type="radio" name="condition" value="Prescription Drug Addiction">
					<label class="reg-label">Prescription Drug Addiction </label><br>
					
					<input type="radio" name="condition" value="Addiction and a psychological/mental illness">
					<label class="reg-label">Addiction and a psychological/mental illness </label><br>
					
					<input type="radio" name="condition" value="Gambling Addiction">
					<label class="reg-label">Gambling Addiction </label><br>
					
					<input type="radio" name="condition" value="Eating Disorder">
					<label class="reg-label">Eating Disorder </label><br>
					
					<input type="radio" name="condition" value="Sex Addiction">
					<label class="reg-label">Sex Addiction </label><br>
					

					<label class="reg-label" for="other-choice">Other</label>
					<input type="text" name="other-choice">
				</div>

				<div class="form-group">
					<label for="long-ago" class="main-label">8. How long has it been since you left treatment?</label>
					<input type="radio" name="long-ago" value="Less than 30 days">
					<label class="reg-label" for="how-choice">Less than 30 days</label><br>
					
					<input type="radio" name="long-ago" value="1 to 3">
					<label class="reg-label">1 to 3 months</label><br>

					<input type="radio" name="long-ago" value="3 to 6">
					<label class="reg-label">3 to 6 months</label><br>

					<input type="radio" name="long-ago" value="6 to 12">
					<label class="reg-label">6 to 12 months</label><br>
					
					<input type="radio" name="long-ago" value="Over 12">
					<label class="reg-label">Over 12 months</label>
				</div>

				<div class="form-group">
					<label for="before-complete" class="main-label">9. Did you leave the treatment center before completing your treatment program?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="before-complete" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="before-complete" value="no"><br>
					<label class="reg-label">If yes, please give a brief explanation</label><br>
					<textarea type="text" name="before-complete-text"></textarea>
				</div>

				<div class="form-group">
					<label for="prescription" class="main-label">10. Do you currently have a prescription to treat your condition?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="prescription" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="prescription" value="no">

				</div>

				<div class="form-group">
					<label for="prescribed" class="main-label">11. If you answered yes to the previous question, was the medication prescribed to you during or after treatment?</label>
					<input type="radio" name="prescribed" value="During treatment">
					<label class="reg-label" for="how-choice">During treatment</label><br>

					<input type="radio" name="prescribed" value="After leaving treatment">
					<label class="reg-label">After leaving treatment</label><br>

					<input type="radio" name="prescribed" value="I was taking medication before I entered treatment">
					<label class="reg-label">I was taking medication before I entered treatment </label>
					
				</div>

				<div class="form-group">
					<label for="sober-living" class="main-label">12. Did you enter sober living after you left the facility?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="sober-living" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="sober-living" value="no">
				</div>

				<div class="form-group">
					<label for="counseling" class="main-label">13. Did you receive counseling?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="counseling" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="counseling" value="no">
				</div>

				<div class="form-group">
					<label class="main-label">14. On a scale of 1 to 5, with 5 being the highest, how satisfied were you with the quality of the counseling services you received?</label>
					<table class="survey-table">
						<tr>
							<th> </th>
							<th>1</th>		
							<th>2</th>
							<th>3</th>
							<th>4</th>
							<th>5</th>
							<th>N/A</th>
						</tr>
						<tr>
							<td>Counselor – level of training and expertise </td>
							<td><input type="radio" name="counselor-level" value="1"></td>
							<td><input type="radio" name="counselor-level" value="2"></td>
							<td><input type="radio" name="counselor-level" value="3"></td>
							<td><input type="radio" name="counselor-level" value="4"></td>
							<td><input type="radio" name="counselor-level" value="5"></td>
							<td><input type="radio" name="counselor-level" value="N/A"></td>
						</tr>
						<tr>
							<td>Counselor’s availability (especially outside of scheduled or office hours) </td>
							<td><input type="radio" name="counselor-available" value="1"></td>
							<td><input type="radio" name="counselor-available" value="2"></td>
							<td><input type="radio" name="counselor-available" value="3"></td>
							<td><input type="radio" name="counselor-available" value="4"></td>
							<td><input type="radio" name="counselor-available" value="5"></td>
							<td><input type="radio" name="counselor-available" value="N/A"></td>
						</tr>
						<tr>
							<td>Respect for patients and their individual needs and preferences </td>
							<td><input type="radio" name="counselor-respect" value="1"></td>
							<td><input type="radio" name="counselor-respect" value="2"></td>
							<td><input type="radio" name="counselor-respect" value="3"></td>
							<td><input type="radio" name="counselor-respect" value="4"></td>
							<td><input type="radio" name="counselor-respect" value="5"></td>
							<td><input type="radio" name="counselor-respect" value="N/A"></td>
						</tr>
						<tr>
							<td>Integration of holistic and alternative treatment options such as yoga, meditation, acupuncture, music therapy, art therapy, animal therapy, spiritual programs, other exercises etc.  </td>
							<td><input type="radio" name="counselor-additional" value="1"></td>
							<td><input type="radio" name="counselor-additional" value="2"></td>
							<td><input type="radio" name="counselor-additional" value="3"></td>
							<td><input type="radio" name="counselor-additional" value="4"></td>
							<td><input type="radio" name="counselor-additional" value="5"></td>
							<td><input type="radio" name="counselor-additional" value="N/A"></td>
						</tr>
						<tr>
							<td>Overall quality of therapy </td>
							<td><input type="radio" name="overall-quality" value="1"></td>
							<td><input type="radio" name="overall-quality" value="2"></td>
							<td><input type="radio" name="overall-quality" value="3"></td>
							<td><input type="radio" name="overall-quality" value="4"></td>
							<td><input type="radio" name="overall-quality" value="5"></td>
							<td><input type="radio" name="overall-quality" value="N/A"></td>
						</tr>
					</table>
				</div>
				
				<div class="form-group">
					<label for="approriate" class="main-label">15. If you were diagnosed with corresponding psychiatric or psychological conditions such as anxiety, depression, PTSD or OCD, were they addressed appropriately during treatment?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="approriate" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="approriate" value="no" >&nbsp;

					<label class="reg-label">Not Applicable</label>
					<input type="radio" name="approriate" value="not applicable">

				</div>

				<div class="form-group">
					<label for="after-care" class="main-label">16. Did the treatment center provide you with resources or aftercare support?</label>
					<label class="reg-label">Yes</label>
					<input type="radio" name="after-care" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="after-care" value="no">
				</div>

				<div class="form-group">
					<label for="relapse" class="main-label">17. Did you relapse after treatment? </label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="relapse" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="relapse" value="no"> &nbsp;

					<label class="reg-label">If yes, how long after?</label>
					<input type="text" class="input-number" name="why-relapse">
				</div>

				<div class="form-group">
					<label for="reenter" class="main-label">18.	Did you re-enter this facility after you relapsed?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="reenter" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="reenter" value="no"> &nbsp;

					<label class="reg-label">I did not relapse </label>
					<input type="radio" name="reenter" value="did not relapse">
				</div>

				<div class="form-group">
					<label for="education" class="main-label">19. Did the treatment program provide you with the appropriate education and recovery tools to help you in your journey toward sobriety?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="education" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="education" value="no"> <br>
					<label class="reg-label">If not, please explain why </label>
					<textarea name="why-education"></textarea>
				</div>

				<div class="form-group">
					<label for="needs" class="main-label">20. Did the program take your specific needs into consideration during individual and group therapy sessions?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="needs" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="needs" value="no"> <br>

					<label class="reg-label">If not, please explain why </label>
					<textarea name="why-needs"> </textarea>
				</div>

				<div class="form-group">
					<label for="expectations" class="main-label">21. Did the addiction center and your specific treatment program meet your needs and expectations?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="expectations" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="expectations" value="no"><br>

					<label class="reg-label">If not, please explain why </label>
					<textarea name="why-expectations"></textarea>
				</div>

				<div class="form-group">
					<label for="recommend" class="main-label">22. Would you recommend this treatment facility to a friend or family member who needs help for their substance dependency?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="recommend" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="recommend" value="no"> <br>

					<label class="reg-label" for="recommend-why">Please explain your answer </label>
					<textarea name="recommend-why"></textarea>
				</div>

				<div class="form-group">
					<label for="return" class="main-label">23.Would you return to this facility if you needed additional treatment or suffered a relapse?</label>
					<label class="reg-label" for="how-choice">Yes</label>
					<input type="radio" name="return" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="return" value="no"> &nbsp;
		 
					<label class="reg-label" for="return-why">Please explain your answer </label>
					<input type="text" name="return-why">
				</div>

				<div class="form-group">
					<label for="rating" class="main-label">24. On a scale of 1 to 5, with 5 being the highest, how satisfied were you with your overall experience and the effectiveness of treatment provided by the facility?</label>
					<input type="text" class="input-number" name="rating">
				</div>

				<div class="form-group">
					<label for="commment" class="main-label">25. In order to further help create a more complete and accurate assessment of your experience, please add any additional information that may be helpful to others in making their decision about the most appropriate program or addiction treatment facility. </label>
					<textarea type="text" name="commment"></textarea>
				</div>

				<div class="form-group multi-text">
					<label class="main-label">26. Would you be willing to have <?= home_url(); ?> contact you for a short follow-up interview? If yes, please provide us with your first name, phone number and/or email address where we can reach you. DrugTreatmentCentersIllinois.com will not use or distribute your information and will not disclose your identify. </label>
					<label for="name" class="reg-label">Name: </label>
					<input type="text" name="name"><br>
					<label for="ph" class="reg-label">Phone Number: </label>
					<input type="text" name="ph"><br>
					<label for="email" class="reg-label">Email: </label>
					<input type="email" name="email">

				</div>

				<?php wp_nonce_field( 'pi_survey_nonce', 'pi_survey_nonce_field' ); ?>
				<input type="hidden" id="survey-type" value="alumni-survey">
				<button type="submit" class="btn btn-default" id="survey-submit">Send Survey</button>
			</form>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content; 
	}
	public function pi_staff_survey(){
		ob_start();
		?>
		<div class="survey-wrapper">

			<h3>I have worked at a Treatment Facility </h3>
			<strong>Please complete this survey if you have worked for or are currently employed with a treatment center. </strong>
			<p>
				In this survey, you are being asked to answer questions on behalf of <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?>. We provide people who are struggling with addiction with the proper resources and help they need. Your answers will help to form a complete and impartial review of the drug treatment center, which in turn, will help our users make the best decision in terms of their specific needs for care. Please take a few minutes to answer these important questions. All answers, unless otherwise requested, will be kept anonymous, and you will never be contacted by <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?> without your consent. Thank you for your contribution.
			</p>
			<form action="<?= htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>" method="post" enctype="multipart/form-data" class="pi-survey">
				<div class="form-group">
					<label for="facility-name" class="main-label">1. What is the name of the facility you worked for?</label>
					<input type="text" name="facility-name">
				</div>

				<div class="form-group multi-text">
					<label class="main-label">2. Location of the Treatment Facility</label>
					
					<label for="city" class="reg-label">City/Town </label>
					<input type="text" name="city"><br>
					
					<label for="state" class="reg-label">State/Provice </label>
					<select type="text" name="state">
						<?php foreach (get_states() as $key => $state) : ?>
							<option value="<?= $key; ?>"><?= $state; ?></option>
						<?php endforeach; ?>
					</select><br>
					
					<label for="country" class="reg-label">Country </label>
					<input type="text" name="country">
				</div>

				<div class="form-group">
					<label class="main-label">3. Are you currently employed with this treatment center?</label>
					<label class="reg-label">Yes</label>
					<input type="radio" name="currently-employed" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="currently-employed" value="no">
				</div>

				<div class="form-group">
					<label class="main-label">4. If your answer to the above question is yes, how long have you been employed there?</label>
					<input type="radio" name="how-long" value="Less than 6 month">
					<label class="reg-label">Less than 6 month</label><br>
					
					<input type="radio" name="how-long" value="6 - 12 months">
					<label class="reg-label"> 6 - 12 months </label><br>

					<input type="radio" name="how-long" value="1 - 5 years">
					<label class="reg-label"> 1 - 5 years</label><br>

					<input type="radio" name="how-long" value="5 - 10 years">
					<label class="reg-label"> 5 - 10 years </label><br>
					
					<input type="radio" name="how-long" value="More than 10 years">
					<label class="reg-label"> More than 10 years </label>
				</div>
				
				<div class="form-group">
					<label for="listing-web" class="main-label">5. What is your role at the treatment center?</label>
					<input type="radio" name="role" value="Executive staff">
					<label class="reg-label">Executive staff</label><br>
					
					<input type="radio" name="role" value="Medical staff">
					<label class="reg-label">Medical staff</label><br>
					
					<input type="radio" name="role" value="Management staff">
					<label class="reg-label">Management staff</label><br>
					
					<input type="radio" name="role" value="Mental health professional">
					<label class="reg-label">Mental health professional</label><br>

					<input type="radio" name="role" value="Administrative staff">
					<label class="reg-label">Administrative staff</label><br>

					<input type="radio" name="role" value="Maintenance">
					<label class="reg-label">Maintenance</label><br>
				
					<label class="reg-label" for="other-role">Other</label>
					<input type="text" name="other-role"><br>
				</div>

				<div class="form-group">
					<label for="listing-web" class="main-label">6. What is your highest level of education?</label>
					<input type="radio" name="education-level" value="High school diploma/GED">
					<label class="reg-label">High school diploma/GED</label><br>
					
					<input type="radio" name="education-level" value="Associate’s degree">
					<label class="reg-label">Associate’s degree</label><br>
					
					<input type="radio" name="education-level" value="Bachelors degree">
					<label class="reg-label">Bachelors degree</label><br>
					
					<input type="radio" name="education-level" value="Masters degree">
					<label class="reg-label">Masters degree</label><br>

					<input type="radio" name="education-level" value="Doctorate">
					<label class="reg-label">Doctorate</label><br>
				
					<label class="reg-label" for="other-education">Other</label>
					<input type="text" name="other-education"><br>
				</div>

				<div class="form-group">
					<label class="main-label">7. Do you work at the treatment center full time or an independent contractor or a consultant?</label>
					<input type="radio" name="full-time-or-contractor" value="Full-time staff membern">
					<label class="reg-label">Full-time staff member</label><br>

					<input type="radio" name="full-time-or-contractor" value="Contractor/Consultant">
					<label class="reg-label">Contractor/Consultant</label><br>

					<label class="reg-label" for="other-full-contractor">Other</label>
					<input type="text" name="other-full-contractor">
				</div>

				<div class="form-group">
					<label for="licensed-medical-professionals" class="main-label">8. How many licensed medical professionals work at the facility full-time?</label>
					<input type="text" name="licensed-medical-professionals">
				</div>

				<div class="form-group">
					<label for="licensed-mental-health-pros" class="main-label">9. How many licensed mental health professionals work there full-time?</label>
					<input type="text" name="licensed-mental-health-pros">
				</div>

				<div class="form-group">
					<label class="main-label">10. How often does the treatment center use the following evidence-based treatment models?</label>
					<table class="survey-table">
						<tr>
							<th>Model</th>
							<th>Never</th>
							<th>Rarely</th>		
							<th>Sometimes</th>
							<th>Often</th>
							<th>Always</th>
							<th>I don't know</th>
						</tr>
						<tr>
							<td>Methadone</td>
							<td><input type="radio" name="methadone" value="Never"></td>
							<td><input type="radio" name="methadone" value="Rarely"></td>
							<td><input type="radio" name="methadone" value="Sometimes"></td>
							<td><input type="radio" name="methadone" value="Often"></td>
							<td><input type="radio" name="methadone" value="Always"></td>
							<td><input type="radio" name="methadone" value="I don't know"></td>
						</tr>
						<tr>
							<td>Antabuse</td>
							<td><input type="radio" name="antabuse" value="Never"></td>
							<td><input type="radio" name="antabuse" value="Rarely"></td>
							<td><input type="radio" name="antabuse" value="Sometimes"></td>
							<td><input type="radio" name="antabuse" value="Often"></td>
							<td><input type="radio" name="antabuse" value="Always"></td>
							<td><input type="radio" name="antabuse" value="I don't know"></td>
					
						</tr>
						<tr>
							<td>Motivational Incentives</td>
							<td><input type="radio" name="motivational-incentives" value="Never"></td>
							<td><input type="radio" name="motivational-incentives" value="Rarely"></td>
							<td><input type="radio" name="motivational-incentives" value="Sometimes"></td>
							<td><input type="radio" name="motivational-incentives" value="Often"></td>
							<td><input type="radio" name="motivational-incentives" value="Always"></td>
							<td><input type="radio" name="motivational-incentives" value="I don't know"></td>
			
						</tr>
						<tr>
							<td>Acamprosate</td>
							<td><input type="radio" name="acamprosate" value="Never"></td>
							<td><input type="radio" name="acamprosate" value="Rarely"></td>
							<td><input type="radio" name="acamprosate" value="Sometimes"></td>
							<td><input type="radio" name="acamprosate" value="Often"></td>
							<td><input type="radio" name="acamprosate" value="Always"></td>
							<td><input type="radio" name="acamprosate" value="I don't know"></td>
						
						</tr>
						<tr>
							<td>Buprenorphine</td>
							<td><input type="radio" name="buprenorphine" value="Never"></td>
							<td><input type="radio" name="buprenorphine" value="Rarely"></td>
							<td><input type="radio" name="buprenorphine" value="Sometimes"></td>
							<td><input type="radio" name="buprenorphine" value="Often"></td>
							<td><input type="radio" name="buprenorphine" value="Always"></td>
							<td><input type="radio" name="buprenorphine" value="I don't know"></td>
						</tr>
						<tr>
							<td>Naltrexone (Tablet or Injectable)</td>
							<td><input type="radio" name="naltrexone" value="Never"></td>
							<td><input type="radio" name="naltrexone" value="Rarely"></td>
							<td><input type="radio" name="naltrexone" value="Sometimes"></td>
							<td><input type="radio" name="naltrexone" value="Often"></td>
							<td><input type="radio" name="naltrexone" value="Always"></td>
							<td><input type="radio" name="naltrexone" value="I don't know"></td>
						</tr>
						<tr>
							<td>Motivational Enhancement Therapy</td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="Never"></td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="Rarely"></td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="Sometimes"></td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="Often"></td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="Always"></td>
							<td><input type="radio" name="motivational-enhancement-therapy" value="I don't know"></td>
						</tr>
						<tr>
							<td>Cognitive Behavioral Therapy</td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="Never"></td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="Rarely"></td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="Sometimes"></td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="Often"></td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="Always"></td>
							<td><input type="radio" name="cognitive-behavioral-therapy" value="I don't know"></td>
						</tr>
						<tr>
							<td>Motivational Interviewing</td>
							<td><input type="radio" name="motivational-interviewing" value="Never"></td>
							<td><input type="radio" name="motivational-interviewing" value="Rarely"></td>
							<td><input type="radio" name="motivational-interviewing" value="Sometimes"></td>
							<td><input type="radio" name="motivational-interviewing" value="Often"></td>
							<td><input type="radio" name="motivational-interviewing" value="Always"></td>
							<td><input type="radio" name="motivational-interviewing" value="I don't know"></td>
						</tr>
					</table>
				</div>

				<div class="form-group">
					<label for="rating" class="main-label">11. On a scale of 1 to 5, with 5 being the highest, how would you rate the facility’s level of care in the treatment of co-occurring mental health disorders? Type "none" if the facility does not offer dual diagnosis treatment </label>
					<input type="text" class="input-number" name="rating">
				</div>

				<div class="form-group">
					<label class="main-label">12. Please rate the management or leadership of the treatment center on a scale of 1 to 5 based on the following criteria: </label>
					<table class="survey-table">
						<tr>
							<th> </th>
							<th>1</th>
							<th>2</th>		
							<th>3</th>
							<th>4</th>
							<th>5</th>
							<th>N/A</th>
						</tr>
						<tr>
							<td>Credentials and experience of the center’s medical staff </td>
							<td><input type="radio" name="medical-staff-credentials" value="1"></td>
							<td><input type="radio" name="medical-staff-credentials" value="2"></td>
							<td><input type="radio" name="medical-staff-credentials" value="3"></td>
							<td><input type="radio" name="medical-staff-credentials" value="4"></td>
							<td><input type="radio" name="medical-staff-credentials" value="5"></td>
							<td><input type="radio" name="medical-staff-credentials" value="N/A"></td>
						</tr>
						<tr>
							<td>Credentials and experience of the mental health staff  </td>
							<td><input type="radio" name="health-staff-credentials" value="1"></td>
							<td><input type="radio" name="health-staff-credentials" value="2"></td>
							<td><input type="radio" name="health-staff-credentials" value="3"></td>
							<td><input type="radio" name="health-staff-credentials" value="4"></td>
							<td><input type="radio" name="health-staff-credentials" value="5"></td>
							<td><input type="radio" name="health-staff-credentials" value="N/A"></td>
						</tr>
						<tr>
							<td>Credentials and experience of the center’s addiction treatment staff</td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="1"></td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="2"></td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="3"></td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="4"></td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="5"></td>
							<td><input type="radio" name="addiction-treatment-staff-credentials" value="N/A"></td>
						</tr>
						<tr>
							<td>Fairness and honesty in terms of advertising, marketing and public relations</td>
							<td><input type="radio" name="marketing-pr-fairness" value="1"></td>
							<td><input type="radio" name="marketing-pr-fairness" value="2"></td>
							<td><input type="radio" name="marketing-pr-fairness" value="3"></td>
							<td><input type="radio" name="marketing-pr-fairness" value="4"></td>
							<td><input type="radio" name="marketing-pr-fairness" value="5"></td>
							<td><input type="radio" name="marketing-pr-fairness" value="N/A"></td>
						</tr>
						<tr>
							<td>Quality of patient care</td>
							<td><input type="radio" name="patient-care-quality" value="1"></td>
							<td><input type="radio" name="patient-care-quality" value="2"></td>
							<td><input type="radio" name="patient-care-quality" value="3"></td>
							<td><input type="radio" name="patient-care-quality" value="4"></td>
							<td><input type="radio" name="patient-care-quality" value="5"></td>
							<td><input type="radio" name="patient-care-quality" value="N/A"></td>
						</tr>
						<tr>
							<td>Willingness and ability to the patients’ needs and interests first </td>
							<td><input type="radio" name="patient-first" value="1"></td>
							<td><input type="radio" name="patient-first" value="2"></td>
							<td><input type="radio" name="patient-first" value="3"></td>
							<td><input type="radio" name="patient-first" value="4"></td>
							<td><input type="radio" name="patient-first" value="5"></td>
							<td><input type="radio" name="patient-first" value="N/A"></td>
						</tr>
						<tr>
							<td>Respect for patient privacy and confidentiality </td>
							<td><input type="radio" name="patient-privacy-respect" value="1"></td>
							<td><input type="radio" name="patient-privacy-respect" value="2"></td>
							<td><input type="radio" name="patient-privacy-respect" value="3"></td>
							<td><input type="radio" name="patient-privacy-respect" value="4"></td>
							<td><input type="radio" name="patient-privacy-respect" value="5"></td>
							<td><input type="radio" name="patient-privacy-respect" value="N/A"></td>
						</tr>
						<tr>
							<td>Respect for treatment protocols</td>
							<td><input type="radio" name="treatment-protocol-respect" value="1"></td>
							<td><input type="radio" name="treatment-protocol-respect" value="2"></td>
							<td><input type="radio" name="treatment-protocol-respect" value="3"></td>
							<td><input type="radio" name="treatment-protocol-respect" value="4"></td>
							<td><input type="radio" name="treatment-protocol-respect" value="5"></td>
							<td><input type="radio" name="treatment-protocol-respect" value="N/A"></td>
						</tr>
						<tr>
							<td>Respect for other staff members</td>
							<td><input type="radio" name="staff-members-respect" value="1"></td>
							<td><input type="radio" name="staff-members-respect" value="2"></td>
							<td><input type="radio" name="staff-members-respect" value="3"></td>
							<td><input type="radio" name="staff-members-respect" value="4"></td>
							<td><input type="radio" name="staff-members-respect" value="5"></td>
							<td><input type="radio" name="staff-members-respect" value="N/A"></td>
						</tr>
						<tr>
							<td>Support for facility protocols and staff member training </td>
							<td><input type="radio" name="staff-members-training-support" value="1"></td>
							<td><input type="radio" name="staff-members-training-support" value="2"></td>
							<td><input type="radio" name="staff-members-training-support" value="3"></td>
							<td><input type="radio" name="staff-members-training-support" value="4"></td>
							<td><input type="radio" name="staff-members-training-support" value="5"></td>
							<td><input type="radio" name="staff-members-training-support" value="N/A"></td>
						</tr>
						<tr>
							<td>Quality of leadership </td>
							<td><input type="radio" name="leadership-quality" value="1"></td>
							<td><input type="radio" name="leadership-quality" value="2"></td>
							<td><input type="radio" name="leadership-quality" value="3"></td>
							<td><input type="radio" name="leadership-quality" value="4"></td>
							<td><input type="radio" name="leadership-quality" value="5"></td>
							<td><input type="radio" name="leadership-quality" value="N/A"></td>
						</tr>
						<tr>
							<td>Ability to manage fairly and inspire a sense of community </td>
							<td><input type="radio" name="community" value="1"></td>
							<td><input type="radio" name="community" value="2"></td>
							<td><input type="radio" name="community" value="3"></td>
							<td><input type="radio" name="community" value="4"></td>
							<td><input type="radio" name="community" value="5"></td>
							<td><input type="radio" name="community" value="N/A"></td>
						</tr>
						<tr>
							<td>Overall satisfaction with your job  </td>
							<td><input type="radio" name="overall-satisfaction" value="1"></td>
							<td><input type="radio" name="overall-satisfaction" value="2"></td>
							<td><input type="radio" name="overall-satisfaction" value="3"></td>
							<td><input type="radio" name="overall-satisfaction" value="4"></td>
							<td><input type="radio" name="overall-satisfaction" value="5"></td>
							<td><input type="radio" name="overall-satisfaction" value="N/A"></td>
						</tr>
					</table>
				</div>

				<div class="form-group">
					<label class="main-label">13. Does the treatment center take insurance?</label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="insurance" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="insurance" value="no">
				</div>

				<div class="form-group">
					<label class="main-label">14.	Does the treatment facility offer any type of financial support? </label>
					<label class="reg-label">Yes</label>
					<input type="radio" name="financial-support" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="financial-support" value="no"> &nbsp;

					<label class="reg-label">I don't know</label>
					<input type="radio" name="financial-support" value="I dont know">
				</div>

				<div class="form-group">
					<label class="main-label">15. Please take a moment to describe what you think are the facility’s strengths</label>
					<textarea name="facility-strengths"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">16. Please take a moment to describe what you think are the facility’s weaknesses </label>
					<textarea name="facility-weaknesses"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">17. Would you recommend this treatment center to a friend, family member or patient?</label>
					<label class="reg-label">Yes</label>
					<input type="radio" name="recommend" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="recommend" value="no"> &nbsp;

					<label class="reg-label">If not, please explain why </label>
					<textarea name="recommend-why"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">18. If you were ever in need of treatment, would you attend this treatment center?</label>
					<label class="reg-label">Yes</label>
					<input type="radio" name="attend" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="attend" value="no"> &nbsp;

					<label class="reg-label">If not, please explain why </label>
					<textarea name="attend-why"></textarea>
				</div>

				<div class="form-group">
					<label for="commment" class="main-label">19. In order to further help create a more complete and accurate assessment of your experience, please add any additional information about your experience working for this facility. </label>
					<textarea name="commment"></textarea>
				</div>

				<div class="form-group multi-text">
					<label class="main-label">20. Would you be willing to have <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>' ;?> contact you for a short follow-up interview? If yes, please provide us with your first name, phone number and/or email address where we can reach you. <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>' ;?> will not use or distribute your information and will not disclose your identify. </label>
					<label for="name" class="reg-label">Name: </label>
					<input type="text" name="name"><br>
					<label for="ph" class="reg-label">Phone Number: </label>
					<input type="text" name="ph"><br>
					<label for="email" class="reg-label">Email: </label>
					<input type="email" name="email">

				</div>

				<?php wp_nonce_field( 'pi_survey_nonce', 'pi_survey_nonce_field' ); ?>
				<input type="hidden" id="survey-type" value="staff-survey">
				<button type="submit" class="btn btn-default" id="survey-submit">Send Survey</button>
			</form>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content; 
	}
	public function pi_family_survey(){
		ob_start();
		?>
		<div class="survey-wrapper">
			<h3>My Friend/Family Member Has Attended Treatment </h3>
			<strong>Complete this survey if you are friends with or related to someone who has attended an addiction treatment center. </strong>
			<p>
				In this survey, you are being asked to answer questions on behalf of <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?>. We provide people who are struggling with addiction with the proper resources and help they need. Your answers will help to form a complete and impartial review of the drug treatment center, which in turn, will help our users make the best decision in terms of their specific needs for care. Please take a few minutes to answer these important questions. All answers, unless otherwise requested,  will be kept anonymous, and you will never be contacted by <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>'; ?> without your consent. Thank you for your contribution.
			</p>
			<form action="<?= htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>" method="post" enctype="multipart/form-data" class="pi-survey">
				<div class="form-group">
					<label class="main-label">1. What is your relationship to the individual who has received treatment for their addiction?</label>
					<input type="radio" name="relationship" value="Daughter/Son">
					<label class="reg-label">Daughter/Son</label><br>
					
					<input type="radio" name="relationship" value="Spouse/Partner">
					<label class="reg-label">Spouse/Partner</label><br>

					<input type="radio" name="relationship" value="Parent">
					<label class="reg-label">Parent</label><br>

					<input type="radio" name="relationship" value="Sibling">
					<label class="reg-label">Sibling</label><br>
					
					<input type="radio" name="relationship" value="Grandparent">
					<label class="reg-label">Grandparent</label><br>
					
					<input type="radio" name="relationship" value="Grandchild">
					<label class="reg-label">Grandchild</label><br>

					<input type="radio" name="relationship" value="Other Family Member">
					<label class="reg-label">Other Family Member </label><br>

					<input type="radio" name="relationship" value="Friend">
					<label class="reg-label">Friend</label><br>

					<input type="radio" name="relationship" value="Co-worker">
					<label class="reg-label">Co-worker</label><br>

					<input type="radio" name="relationship" value="Doctor/Therapist">
					<label class="reg-label">Doctor/Therapist</label><br>
					
					<label class="reg-label">Other (please specify)</label>
					<input type="text" name="relationship-other">
				</div>
				<div class="form-group">
					<label for="facility-name" class="main-label">2. Which treatment center did that person attend?</label>
					<input type="text" name="facility-name">
				</div>

				<div class="form-group multi-text">
					<label class="main-label">3. Location of the Treatment Facility</label>
					
					<label for="city" class="reg-label">City/Town </label>
					<input type="text" name="city"><br>
					
					<label for="state" class="reg-label">State/Provice </label>
					<select type="text" name="state">
						<?php foreach (get_states() as $key => $state) : ?>
							<option value="<?= $key; ?>"><?= $state; ?></option>
						<?php endforeach; ?>
					</select><br>
					
					<label for="country" class="reg-label">Country </label>
					<input type="text" name="country">
				</div>

				<div class="form-group">
					<label class="main-label">4. When did they enter the facility?</label>
					<input type="text" class="input-number" name="date-month" placeholder="MM"> /
					<input type="text" class="input-number" name="date-day" placeholder="DD"> /
					<input type="text" class="input-number" name="date-year" placeholder="YY">
				</div>


				<div class="form-group">
					<label class="main-label">5. What did they receive treatment for?</label>
					<input type="radio" name="treatment-for" value="Alcohol addiction">
					<label class="reg-label">Alcohol addiction</label><br>
					
					<input type="radio" name="treatment-for" value="Illegal drug addiction">
					<label class="reg-label">Illegal drug addiction</label><br>

					<input type="radio" name="treatment-for" value="Prescription drug addiction">
					<label class="reg-label">Prescription drug addiction</label><br>

					<input type="radio" name="treatment-for" value="Addiction and a psychological/mental illness">
					<label class="reg-label">Addiction and a psychological/mental illness</label><br>
					
					<input type="radio" name="treatment-for" value="Gambling addiction">
					<label class="reg-label">Gambling addiction</label><br>

					<input type="radio" name="treatment-for" value="Eating disorder">
					<label class="reg-label">Eating disorder</label><br>

					<input type="radio" name="treatment-for" value="Sex addiction">
					<label class="reg-label">Sex addiction</label><br>
					
					<label class="reg-label">Other </label>
					<input type="text" name="treatment-for-other">
				</div>
				
				<div class="form-group">
					<label class="main-label">6. How long did they stay at the treatment center?</label>
					<input type="radio" name="how-long" value="Less than 15 days">
					<label class="reg-label"> Less than 15 days </label><br>
					
					<input type="radio" name="how-long" value="15-30 days">
					<label class="reg-label">15-30 days</label><br>
					
					<input type="radio" name="how-long" value="30-60 days">
					<label class="reg-label">30-60 days</label>
				</div>

				<div class="form-group">
					<label class="main-label">7. Did you take on an active role in helping this person make the decision to enter treatment?</label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="active-role" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="active-role" value="no">
				</div>

				<div class="form-group">
					<label class="main-label">8. If you were involved in helping them make their choice, what resources did you use?</label>
					<input type="radio" name="resources" value="Treatment center's website">
					<label class="reg-label">Treatment center's website </label><br>

					<input type="radio" name="resources" value="Online database">
					<label class="reg-label">Online database</label><br>

					<input type="radio" name="resources" value="Referral from a doctor">
					<label class="reg-label">Referral from a doctor</label><br>

					<input type="radio" name="resources" value="Referral from a friend, co-worker, other family member, etc">
					<label class="reg-label">Referral from a friend, co-worker, other family member, etc.</label><br>

					<label class="reg-label" for="other-resources">Other</label>
					<input type="text" name="other-resources">
				</div>

				<div class="form-group">
					<label class="main-label">9. Did you visit him/her while they were in treatment?</label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="visit" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="visit" value="no"><br>

					<label class="reg-label" for="visit-why">If no, Why not?</label>
					<textarea name="visit-why"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">10. Did you keep in contact with the patient during his/her stay in the facility? </label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="contact" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="contact" value="no"><br>

					<label class="reg-label" for="contact-why">If no, Why not?</label>
					<textarea name="contact-why"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">11. Did the treatment center allow for family events and/or family therapy sessions? </label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="family" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="family" value="no"><br>

					<label class="reg-label" for="family-why">If no, Why not?</label>
					<textarea name="family-why"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">12. How long has it been since the patient left the treatment facility?</label>
					<input type="radio" name="how-long-since" value="Less than 30 days">
					<label class="reg-label"> Less than 30 days </label><br>
					
					<input type="radio" name="how-long-since" value="1 to 3 months">
					<label class="reg-label"> 1 to 3 months </label><br>
					
					<input type="radio" name="how-long-since" value="3 to 6 months">
					<label class="reg-label"> 3 to 6 months </label><br>

					<input type="radio" name="how-long-since" value="6 to 12 months">
					<label class="reg-label"> 6 to 12 months  </label><br>

					<input type="radio" name="how-long-since" value="Over 12 months">
					<label class="reg-label"> Over 12 months  </label>
				</div>

				<div class="form-group">
					<label class="main-label">13. Did they or will they receive aftercare services?</label>
					
					<label class="reg-label"> Yes </label>
					<input type="radio" name="aftercare" value="Yes"> &nbsp; 
					
					<label class="reg-label"> No </label>
					<input type="radio" name="aftercare" value="No"> &nbsp;
					
					<label class="reg-label"> I don't know </label>
					<input type="radio" name="aftercare" value="dont-know">
				</div>

				<div class="form-group">
					<label class="main-label">14. If they did receive aftercare services, how much involvement did the treatment center have in this process?</label>
					
					<input type="radio" name="treatment-center-involvement-in-aftercare" value="A lot of involvement">
					<label class="reg-label"> A lot of involvement </label><br>
					
					<input type="radio" name="treatment-center-involvement-in-aftercare" value="Some involvement">
					<label class="reg-label"> Some involvement  </label><br>
					
					<input type="radio" name="treatment-center-involvement-in-aftercare" value="No involvement">
					<label class="reg-label"> No involvement </label><br>

					<input type="radio" name="treatment-center-involvement-in-aftercare" value="I don't know">
					<label class="reg-label"> I don't know  </label>
				</div>

				<div class="form-group">
					<label class="main-label">15. Are you currently actively involved in this person’s recovery?</label>
					<label class="reg-label" >Yes</label>
					<input type="radio" name="currently-involved" value="yes"> &nbsp;

					<label class="reg-label">No</label>
					<input type="radio" name="currently-involved" value="no"><br>

					<label class="reg-label" for="currently-involved-why">If no, Why not?</label>
					<textarea name="currently-involved"></textarea>
				</div>

				<div class="form-group">
					<label class="main-label">16. Do you feel this person has benefitted from their time at the treatment facility?</label>
					
					<label class="reg-label"> Yes </label>
					<input type="radio" name="benefitted" value="Yes"> &nbsp; 
					
					<label class="reg-label"> No </label>
					<input type="radio" name="benefitted" value="No"> &nbsp;
					
					<label class="reg-label"> I don't know </label>
					<input type="radio" name="benefitted" value="dont-know">
				</div>

				<div class="form-group">
					<label class="main-label">17. If your answer to the previous question was yes, in what area of their life do you see the most improvement? (You can choose multiple answers)</label>
					<input type="checkbox" name="improvement" value="He/she has stopped using drugs/alcohol">
					<label class="reg-label">He/she has stopped using drugs/alcohol</label><br>

					<input type="checkbox" name="improvement" value="He/she is no longer engaging in harmful/addictive/dangerous behaviors">
					<label class="reg-label">He/she is no longer engaging in harmful/addictive/dangerous behaviors</label><br>

					<input type="checkbox" name="improvement" value="Improvement in their attitude and/or mood">
					<label class="reg-label">Improvement in their attitude and/or mood </label><br>

					<input type="checkbox" name="improvement" value="They are making better life choice, including financial and career decisions">
					<label class="reg-label">They are making better life choice, including financial and career decisions </label><br>

					<input type="checkbox" name="improvement" value="They have learned valuable life skills such as how to better manage their stress, anxiety and anger">
					<label class="reg-label">They have learned valuable life skills such as how to better manage their stress, anxiety and anger </label><br>

					<input type="checkbox" name="improvement" value="Confidence has improved">
					<label class="reg-label">Confidence has improved</label><br>

					<input type="checkbox" name="improvement" value="Their involvement in family and/ or group activities has increased">
					<label class="reg-label">Their involvement in family and/ or group activities has increased</label><br>

					<input type="checkbox" name="improvement" value="Improvement of communication skills">
					<label class="reg-label">Improvement of communication skills </label><br>

					<input type="checkbox" name="improvement" value="Willingness to continue going to meetings and/or ask for help when they need it">
					<label class="reg-label">Willingness to continue going to meetings and/or ask for help when they need it  </label><br>

					<label class="reg-label" for="other-improvement">Other</label>
					<input type="text" name="other-improvement">
				</div>

				<div class="form-group">
					<label class="main-label">18. If this person has not shown improvement in any of these areas, please describe where you think their treatment fell short.</label>
					<textarea name="fell-short"></textarea>
				</div>

				<div class="form-group">
					<label for="rating" class="main-label">19. On a scale of 1 to 5, with 5 being the highest, how would you rate the effectiveness of treatment provided to your loved one at the treatment facility? </label>
					<input type="text" class="input-number" name="rating">
				</div>

				<div class="form-group">
					<label for="comment" class="main-label">20. In order to further help create a more complete and accurate assessment of your experience, please add any additional information that may be helpful to others in making their decision about the most appropriate program or addiction treatment facility. </label>
					<textarea name="comment"></textarea>
				</div>

				<div class="form-group multi-text">
					<label class="main-label">21.	Would you be willing to have <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>' ;?> contact you for a short follow-up interview? If yes, please provide us with your first name, phone number and/or email address where we can reach you. <?= '<a href="'. home_url() .'">'. get_bloginfo('name') .'</a>' ;?> will not use or distribute your information and will not disclose your identify.</label>
					<label for="name" class="reg-label">Name: </label>
					<input type="text" name="name"><br>
					<label for="ph" class="reg-label">Phone Number: </label>
					<input type="text" name="ph"><br>
					<label for="email" class="reg-label">Email: </label>
					<input type="email" name="email">

				</div>

				<?php wp_nonce_field( 'pi_survey_nonce', 'pi_survey_nonce_field' ); ?>
				<input type="hidden" id="survey-type" value="family-survey">
				<button type="submit" class="btn btn-default" id="survey-submit">Send Survey</button>
			</form>
		</div>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content; 
	}
	/**
	*
	* Validate listing before saving in front end
	*
	**/
	public function pi_process_alumni_survey( $posted ){
		$content = '';
		foreach ($posted as $key => $value) {
			if( $key === 'pi_survey_nonce_field' || $key === '_wp_http_referer' || $key ===  'survey_submitted'){
				$content .= '';
			}else{
				if( !empty($value) ){
					$value = trim($value);
					switch( $key ){
						case 'email':
							if( is_email( $value ) ){
								$header = 'Email: ';
								$value = sanitize_email( $value );
								$content .= $header . ': ' .  $value . '<br>';
							}
							break;
						case 'date-month':
							$content .= 'Date began treatment: '. sanitize_text_field($value) . '/' ;
							break;
						case 'date-day':
							$content .= sanitize_text_field($value) . '/';
							break;
						case 'date-year':
							$content .= sanitize_text_field($value) . '<br>';
							break;
						case 'facility-name':
							$value = sanitize_text_field( $value );
							$content .= 'Facility Name: ' .  $value . '<br>';
							break;
						case 'led-decision':
							$header = 'What led to the decision? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'how-choice':
							$header = 'How did you choose this treatment facility? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'condition':
							$header = 'What condition were you treated for? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'long-ago':
							$header = 'How long has it been since you left treatment? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'before-complete':
							$header = 'Did you leave the treatment center before completing your treatment program? If yes, explain. ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'counselor-additional':
							$header = 'Integration of holistic and alternatice treatments...';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'approriate':
							$header = 'If you were diagnosed with corresponding psychiatric or psychological conditions such as anxiety, depression, PTSD or OCD, were they addressed appropriately during treatment? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'after-care':
							$header = 'Did the treatment center provide you with resources or aftercare support? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'relapse':
							$header = 'Did you relapse after treatment? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'reenter':
							$header = 'Did you re-enter this facility after you relapsed? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'education':
							$header = 'Did the treatment program provide you with the appropriate education and recovery tools to help you in your journey toward sobriety? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'needs':
							$header = 'Did the program take your specific needs into consideration during individual and group therapy sessions? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'expectations':
							$header = 'Did the addiction center and your specific treatment program meet your needs and expectations? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'recommend':
							$header = 'Would you recommend this treatment facility to a friend or family member who needs help for their substance dependency? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						case 'return':
							$header = 'Would you return to this facility if you needed additional treatment or suffered a relapse? ';
							$value = sanitize_text_field( $value );
							$content .= $header . ': ' .  $value . '<br>';
							break;
						default:
							$header =  str_replace("-"," ",$key); 
							$header = ucwords($header);
							$value = sanitize_text_field($value);
							$content .= $header . ': ' .  $value . '<br>'; 
					}				
				}
			}
		}
		return $content;	
	}
	/**
	*
	* Validate listing before saving in front end
	*
	**/
	public function pi_process_staff_survey( $posted ){
		$content = '';
		foreach ($posted as $key => $value) {
			if( $key === 'pi_survey_nonce_field' || $key === '_wp_http_referer' || $key ===  'survey_submitted'){
				$content .= '';
			}else{
				if( !empty($value) ){
					$value = trim($value);
					switch( $key ){
						case 'email':
							if( is_email( $value ) ){
								$header = 'Email: ';
								$value = sanitize_email( $value );
								$content .= $header . ': ' .  $value . '<br>';
							}
							break;
						case 'currently-employed':
							$value = sanitize_text_field( $value );
							$content .= 'Are you currently employed with this treatment center? ' .  $value . '<br>';
							break;
						case 'how-long':
							$value = sanitize_text_field( $value );
							$content .= 'How long have you been employed there? ' .  $value . '<br>';
							break;
						case 'full-time-or-contractor':
							$value = sanitize_text_field( $value );
							$content .= 'Do you work at the treatment center full time or an independent contractor or a consultant? ' .  $value . '<br>';
							break;
						case 'licensed-medical-professionals':
							$value = sanitize_text_field( $value );
							$content .= 'How many licensed medical professionals work at the facility full-time? ' .  $value . '<br>';
							break;
						case 'medical-staff-credentials':
							$value = sanitize_text_field( $value );
							$content .= 'Credentials and experience of the center’s medical staff ' .  $value . '<br>';
							break;
						case 'health-staff-credentials':
							$value = sanitize_text_field( $value );
							$content .= 'Credentials and experience of the mental health staff ' .  $value . '<br>';
							break;
						case 'addiction-treatment-staff-credentials':
							$value = sanitize_text_field( $value );
							$content .= 'Credentials and experience of the center\'s addiction treatment staff ' .  $value . '<br>';
							break;
						case 'marketing-pr-fairness':
							$value = sanitize_text_field( $value );
							$content .= 'Fairness and honesty in terms of advertising, marketing and public relations ' .  $value . '<br>';
							break;
						case 'community':
							$value = sanitize_text_field( $value );
							$content .= 'Ability to manage fairly and inspire a sense of community ' .  $value . '<br>';
							break;
						default:
							$header =  str_replace("-"," ",$key); 
							$header = ucwords($header);
							$value = sanitize_text_field($value);
							$content .= $header . ': ' .  $value . '<br>'; 
					}				
				}
			}
		}
		return $content;	
	}
	/**
	*
	* Validate listing before saving in front end
	*
	**/
	public function pi_process_family_survey( $posted ){
		$content = '';
		foreach ($posted as $key => $value) {
			if( $key === 'pi_survey_nonce_field' || $key === '_wp_http_referer' || $key ===  'survey_submitted'){
				$content .= '';
			}else{
				if( !empty($value) ){
					$value = trim($value);
					switch( $key ){
						case 'email':
							if( is_email( $value ) ){
								$header = 'Email: ';
								$value = sanitize_email( $value );
								$content .= $header . ': ' .  $value . '<br>';
							}
							break;
						case 'currently-employed':
							$value = sanitize_text_field( $value );
							$content .= 'Are you currently employed with this treatment center? ' .  $value . '<br>';
							break;
						case 'relationship':
							$value = sanitize_text_field( $value );
							$content .= 'What is your relationship to the individual who has received treatment for their addiction? ' .  $value . '<br>';
							break;
						case 'date-month':
							$content .= 'When did they enter the facility: '. sanitize_text_field($value) . '/' ;
							break;
						case 'date-day':
							$content .= sanitize_text_field($value) . '/';
							break;
						case 'date-year':
							$content .= sanitize_text_field($value) . '<br>';
							break;
						case 'active-role':
							$value = sanitize_text_field( $value );
							$content .= 'Did you take on an active role in helping this person make the decision to enter treatment? ' .  $value . '<br>';
							break;
						case 'resources':
							$value = sanitize_text_field( $value );
							$content .= 'If you were involved in helping them make their choice, what resources did you use? ' .  $value . '<br>';
							break;
						case 'contact':
							$value = sanitize_text_field( $value );
							$content .= 'Did you keep in contact with the patient during his/her stay in the facility? ' .  $value . '<br>';
							break;
						case 'family':
							$value = sanitize_text_field( $value );
							$content .= ' Did the treatment center allow for family events and/or family therapy sessions? ' .  $value . '<br>';
							break;
						case 'benefitted':
							$value = sanitize_text_field( $value );
							$content .= ' Do you feel this person has benefitted from their time at the treatment facility? ' .  $value . '<br>';
							break;
						case 'improvement':
							$value = sanitize_text_field( $value );
							$content .= ' If your answer to the previous question was yes, in what area of their life do you see the most improvement? (You can choose multiple answers) ' .  $value . '<br>';
							break;
						default:
							$header =  str_replace("-"," ",$key); 
							$header = ucwords($header);
							$value = sanitize_text_field($value);
							$content .= $header . ': ' .  $value . '<br>'; 
					}				
				}
			}
		}
		return $content;	
	}
}
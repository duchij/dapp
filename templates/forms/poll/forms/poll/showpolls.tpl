<input type="hidden" value="polls" id="tplform">
<div class="row padded">
<h1 class="blue"></h1>

{*$polls|@var_dump*}

<div id="infoMessage"></div>

{assign var="sessionData" value=$smarty.session._sf2_attributes.election2017data}

{*$sessionData|@var_dump*}


{if $noPolls}
<h2 class="asphalt">{$noPolls}</h1>
{else}
{foreach from=$polls item=poll key=k}
		<div class="row">
			<div class="box">
				<h2 class="asphalt">{$poll.poll_title}</h2>
				<hr>
				Poll start: <strong>{$poll.poll_start_date}</strong> Poll end: <strong>{$poll.poll_end_date}</strong>
				<hr>
				<div>{$poll.poll_description}</div>
				<hr>
				
				
			{if $poll.poll_status=='voted' && $poll.user_hash==$sessionData.user_hash } 
					<div class="alert message">
					<span class="red large">You have already put your vote...</span>
					<p>{*FROM: <span class="red">{$poll.ip_adres}</span>*}
					Date:<span class="blue"> {$poll.vote_date}</span></p>
					
					{if $poll.poll_m_answers}
						{if $poll.user_hash == $sessionData.user_hash}
							ANSWER(S):
							  <ol>
								{foreach from=$poll.poll_m_answers item=answer}
									<li class="asphalt"><strong>{$answer}</strong></li>
									{/foreach}
							</ol>
						{/if}
					
					
					{else}
					
						{if $poll.user_hash == $sessionData.user_hash}
							ANSWER:<span class="asphalt"> {$poll.poll_answer}</span>
						{/if}
					{/if}
					</div>
					
			{elseif $poll.poll_status == 'voting' && $poll.user_hash==$sessionData.user_hash}
			
					<input type="hidden" name="possible_answers" id="paHd_{$poll.poll_id}" value="{$poll.answer_count}">
					<input type="hidden" name="voted_data" id="votingHd_{$poll.poll_id}" value="{$poll.poll_answer}">
			
					<div class="asphalt message padded">Your Votes....
					<ol>
						{foreach from=$poll.poll_m_answers item=nAnswer}
							<li>{$nAnswer}</li>
						{/foreach}
					</ol>
					You have to choose another answer! 	Possible choices {$poll.answer_count}		
					
					</div>
					
					
					{foreach from=$poll.poll_stats item=stat key=s}
						<button class="green button" onclick="selectPoll('{$stat}',{$poll.poll_id});">{$stat}</button>					
					{/foreach}
			
					
					
			{else}
				Your vote:
				<input type="hidden" name="possible_answers" id="paHd_{$poll.poll_id}" value="{$poll.answer_count}">
				<input type="hidden" name="voted_data" id="votingHd_{$poll.poll_id}" value="">
				
				{if $poll.v_status=='open'}
				
					{foreach from=$poll.poll_stats item=stat key=s}
						<button class="green button" onclick="selectPoll('{$stat}',{$poll.poll_id});">{$stat}</button>					
					{/foreach}
				{else}
					<h3 class="red">Voting was closed</h3>
				{/if}
			{/if}
					{if $sessionData.account_type == 'superadmin'}
					<div>
						{if $poll.poll_active == 1}
							<button id="editVote_{$poll.poll_id}" class="asphalt button">Edit</button>
						{/if}
						{if $poll.v_status=="open"}
							<button id="showResults_{$poll.poll_id}" class="blue button">Show preliminary results</button>
						{/if}
						{if $poll.v_status=="closed"}
							<button id="showDefResults_{$poll.poll_id}" class="blue button">Show definitive results</button>
						{/if}	
						{if $poll.poll_active == 1}
							<button id="disableVote_{$poll.poll_id}" class="red button" style="float:right;">Disable the vote</button>
						{/if}
					</div>
					{/if}
					<hr>
					{if $poll.poll_status!="closed"}
						<div class="error-bg">Once you vote, your vote is FINAL and can not be changed!</div>		
					{/if}
			</div>
		
		
		</div>
		<br>
{/foreach}
{/if}
</div>
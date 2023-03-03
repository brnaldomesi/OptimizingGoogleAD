<template>
	<div class="Feed" style="width: 100%;">
    <main>
      <div class="rounded bg-white mb-10 "style="max-width:720px;margin: 50px auto 0 auto;">
        <div class="flex flex-wrap justify-between" style="padding: 20px;">
          <div class="flex flex-wrap">
            <h2 style="font-weight: 700;font-size: large;width: 100%;">COMING SOON</h2>
            <div class="flex-grow mr-4" style="max-width: 100%;">
              <p class="card-title mt-1">What are we working on, right now?</p> 
              <div>
				<p>Entry, 21st May 2020 | ETA, 8 weeks</p>
				<p>I've been dragging the AdEvolver development left, right, down and up… I slowed us down! Thankfully, I've learnt my lesson. On reflection it means we need to focus and do one thing <em>really</em> well, better than anyone else does it.</p> 
				<p>Over the coming weeks we're doubling down on BudgetCommander. That also means we probably won't release the Ad Tester and Evo Feed until we're done with budgets - sorry!</p>
				<p>The initial feature set is solid, when we release Automation (weeks, if not days) it will be potent. However, we’re giving it yet more power with <span style="color: #CA2D78">Budget Groups</span>.</p>
				<p>Most Google Ad accounts will benefit from slicing and optimising budgets, even the smaller ones.</p>
				<p><center> <i class="fa fa-bullhorn" aria-hidden="true"></i><em> As ever what you see here is all work in progress, the images are mockups - done by me. <br>They aren’t the highest fidelity – but should give you a good idea!</em></center>
				<p>Let me walk you through the features:</p>
				<p class="card-title mt-1">Budget Settings</p>
				<p>If you don’t use <span style="color: #CA2D78">Budget Groups</span> then the settings section remains in play and still the same.</p>
				<p>If you do use groups, then the settings reflect your total budget and blended KPI from all groups.</p>
				<p>As a result, the budget and KPI fields will be locked out. All other features will remain available.</p>
				<p><img class='mt-10' src='/assets/img/coming-soon/budget-settings.png'></p>
				<p>Next, we add the area for you to actually create things – below the BudgetCommander chart and settings:</p>
				<p><img class='mt-10' src='/assets/img/coming-soon/create-budget-groups.png'></p>
				<p>I will of course be doing a video tutorial and overview of <span style="color: #CA2D78">Budget Groups</span>. Furthermore, I plan to create adjoining resources for each main feature – for example, a "budget management 101". </p>
				<p>My vision is for all help files to be accompanied by in-app training.</p>
				<p>So, what happens when you create a group?</p>
				<ul class="soon"><li>Toggle-select the campaigns you want</li>
				<li>Give your group a name</li>
				<li>the target spend</li>
				<li>target KPI for this group</li>
				<li>finally, set it to be a continuous budget or scheduled</li>
				<li>Save and you’re done</li>
				</ul>	
				<p><img class='mt-10' src='/assets/img/coming-soon/my-first-budget.png'></p>
				<p>By default, as soon as you create just 1 group, we add all other campaigns to the "Everything else" group. This group gets the remaining budget, based on your total set when you sync your account.</p>
				<p><em>Still with me?</em></p>
				<p class="card-title mt-1">Budget Groups overview</p>
				<p>The overview table will look something like this. Each group, it’s date range, target, spend and pacing. Also, with the option to include in Automation.</p>
				<p>You will be able to edit all groups from this interface once you click "Edit Budget", otherwise it is locked from accidental clicks.</p>
				<p><img class='mt-10' src='/assets/img/coming-soon/Budget-group-summary.png'></p>
				<p>Naturally, you might need to delete budgets. Again, accessed by clicking Edit and by clicking the bin/trash icon.  You will be required to confirm this action!</p>
				<p><img class='mt-10' src='/assets/img/coming-soon/delete-confirm.png'></p>
				<p>Beyond this next release, we will work on a change log and undo facility. That way you’ll be able to remind yourself of past budgets and undo any mishaps.</p>
				<p>Finally, we’ll update the chart to allow you to overlay different budgets and KPIs together – this should make managing large and complex budgets much easier.</p>
				<p>If you have any feedback, please hop in to Slack and let me know your thoughts.</p>
				<p>Thanks for reading,</p>
				<p>Ed, Charles and the team</p>

              </div>
              </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: "feed_holding",
};
</script>

<style>
p{
  line-height:1.8rem;margin-bottom:1.5rem;
}
ul.soon {
  list-style-type: circle;margin:0 0 30px 30px;
}
  </style>

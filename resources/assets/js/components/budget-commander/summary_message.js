export function getSummaryMessage(
  budget,
  rollover_spend,
  currency_symbol,
  forecast_spend,
  spend,
  kpi_option,
  kpi_value,
  kpi_target
) {
  let summary_message = "";
  const date = new Date();
  const month = date.toLocaleString("default", { month: "long" });
  if (date.getDate() === 1) {
    let message = [];
    let total_budget = budget;
    if (rollover_spend > 0) {
      message.push(
        currency_symbol +
          "" +
          rollover_spend +
          " of your budget went unspent last month."
      );
      total_budget += rollover_spend;
    }
    message.push(
      "Your new total budget for " +
        month +
        " is " +
        currency_symbol +
        "." +
        Number(total_budget).toLocaleString()
    );
    summary_message = message.join(" ");
    return summary_message;
  }

  if (date.getDate() < 6) {
    return "It's a bit early in the month to make reliable forecast";
  }

  let days_in_month = new Date(
    new Date().getFullYear(),
    new Date().getMonth() + 1,
    0
  ).getDate();
  let remaining_days = days_in_month - (new Date().getDate() - 1);
  let remaining_budget = budget - spend;
  let daily_spend = forecast_spend / days_in_month;

  let message = [];

  let vs_budget_percentage = forecast_spend / budget;
  // console.log("vs_budget_percentage", vs_budget_percentage);
  let vs_kpi_percentage = getVsKpiPercentage(kpi_value, kpi_target, kpi_option);

  function getKpiDisplayPercentage(kpi_option, kpi_target, kpi_value) {
    let kpi_display_percentage =
      vs_kpi_percentage > 1
        ? Math.round((kpi_value / kpi_target) * 100 - 100)
        : Number(Math.round((kpi_target / kpi_value) * 100 - 100));
    return Math.abs(vs_kpi_percentage).toFixed(1) + "%.";
  }

  let daily_spend_message =
    "Daily spend is currently " +
    currency_symbol +
    "" +
    Number(daily_spend)
      .toFixed(2)
      .toLocaleString() +
    ".";

  const target_verb = kpi_option === "CPA" ? "reduce" : "improve";

  const under_over = getUnderOrOver(kpi_option, vs_kpi_percentage);

  if (
    vs_budget_percentage >= 0.9 &&
    vs_budget_percentage <= 1.05 &&
    kpiWithinFivePercent(vs_kpi_percentage)
  ) {
    // console.log('message 1')
    message.push("Looking good!");
    message.push(daily_spend_message);
    message.push("You're on track to hit your spend and KPI targets.");
    return message.join(" ");
  }

  if (vs_budget_percentage < 0.9 && kpiWithinFivePercent(vs_kpi_percentage)) {
    // console.log('message 2')
    message.push(daily_spend_message);
    message.push(
      "You're behind on spend but on target to hit your ideal " +
        kpi_option +
        "."
    );
    return message.join(" ");
  }

  if (
    vs_budget_percentage < 0.9 &&
    isBehindKpiTarget(kpi_option, vs_kpi_percentage)
  ) {
    // console.log('message 3')
    message.push(daily_spend_message);
    message.push(
      "You're under budget and " +
        under_over +
        " your target " +
        kpi_option +
        " by " +
        getKpiDisplayPercentage(kpi_option, kpi_target, kpi_value)
    );
    message.push(
      "Consider enabling Budget Automation to " +
        target_verb +
        " your " +
        kpi_option +
        "."
    );
    return message.join(" ");
  }

  if (
    vs_budget_percentage < 0.9 &&
    isAheadOfKpiTarget(kpi_option, vs_kpi_percentage)
  ) {
    // console.log('message 4')
    message.push(daily_spend_message);
    message.push(
      "You're under budget and " +
        under_over +
        " your target " +
        kpi_option +
        " by " +
        getKpiDisplayPercentage(kpi_option, kpi_target, kpi_value)
    );
    message.push(
      "Consider enabling Budget Automation to help lift spend at your target " +
        kpi_option +
        "."
    );
    return message.join(" ");
  }

  // if(vs_budget_percentage > 1 && isBehindKpiTarget(kpi_option, vs_kpi_percentage)){
  //   console.log('message 5')
  //   message.push(daily_spend_message)
  //   message.push("You're over budget and "+under_over+" your target "+kpi_option+" by "+getKpiDisplayPercentage(kpi_option,kpi_target, kpi_value))
  //   message.push("Consider enabling Budget Automation to help lift spend at your target "+kpi_option+".")
  //   return message.join(' ')
  // }

  return daily_spend_message;
}

function getVsKpiPercentage(kpi_value, kpi_target, kpi_option) {
  if (Math.min(kpi_value, kpi_target) < 0.1) return 0;
  return ((kpi_value - kpi_target) / kpi_target) * 100;
}

function getUnderOrOver(kpi_option, vs_kpi_percentage) {
  //options are under and above (target)
  if (kpi_option == "ROAS") return vs_kpi_percentage < 0 ? "under" : "over";
  return vs_kpi_percentage < 0 ? "under" : "over";
}

function isBehindKpiTarget(kpi_option, vs_kpi_percentage) {
  if (kpi_option == "CPA") {
    return vs_kpi_percentage <= 0 ? false : true;
  }
  return vs_kpi_percentage <= 0 ? true : false;
}

function kpiWithinFivePercent(vs_kpi_percentage) {
  return vs_kpi_percentage >= -5 && vs_kpi_percentage <= 5 ? true : false;
}

function isAheadOfKpiTarget(kpi_option, vs_kpi_percentage) {
  //5% or more better than target
  if (kpi_option == "CPA") {
    return vs_kpi_percentage <= 0.95 ? true : false;
  }
  return vs_kpi_percentage >= 1.05 ? true : false;
}

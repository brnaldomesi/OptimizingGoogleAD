@extends('layouts.app')

@section('pageTitle')
    Payment
@endsection


@section('breadcrumbs')

@endsection

@section('content')
    <h4>The plan that you have selected will allow <strong>{{ $plan['user_limit'] }}</strong> user/s to manage <strong>{{ $plan['account_limit'] }} accounts</strong>.</h4>
    <h4>You will be automatically billed <strong>@money($plan->price, $plan->currency) every {{ $plan['frequency'] }}.</strong></h4>
    <hr>
    <form action="{{ url('/user/subscription/add') }}" method="post" id="payment-form" class="form">
        @csrf
        <input type="hidden" value="" name="payment_method" id="payment-method-id">
        <input type="hidden" name="plan" value="{{ $plan['id'] }}">
        {{--            <input type="hidden" name="resume-plan" value="{{ $resumeplan }}">--}}
        <div class="form-group{{ $errors->has('card-holder-name') ? ' has-error' : '' }} required" style="width:30%;">
            <label for="card-holder-name">Name on Card *</label>
            <input class="form-control" id="card-holder-name" type="text" style="border-radius: 4px;" required>
        </div>

        <br>
            <label for="card-element">
                Credit or Debit Card *
            </label>
            <div class="form-control col-md-4"  id="card-element"></div>
            <div id="card-errors" role="alert"></div>
        <br />

        <br />

        <div class="form-row">
            <button id="card-button" class="btn btn-primary" data-secret="{{ $intent->client_secret }}">
                Submit Payment
            </button>
        </div>
    </form>

    <script src="https://js.stripe.com/v3/"></script>

    <script>
       (function() {

            const stripe = Stripe('{{ config('cashier.key') }}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');

            const cardHolderName = document.getElementById('card-holder-name');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;


            cardButton.addEventListener('click', async (e) => {
                e.preventDefault();

                const { setupIntent, error } = await stripe.handleCardSetup(
                    clientSecret, cardElement, {
                        payment_method_data: {
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );

                if (error) {
                    console.log(error);
                } else {
                    // The card has been verified successfully...
                    let paymentMethodID = setupIntent.payment_method;
                    console.log(setupIntent);
                    $("#payment-method-id").val(paymentMethodID);
                    $("#card-button").attr("disabled", true);
                    $("#card-button").text("Processing...");
                    $("#payment-form").submit();
                }
            });
       })();
    </script>

@endsection


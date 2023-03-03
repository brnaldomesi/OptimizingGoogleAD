@extends('layouts.app')

@section('pageTitle')
    Subscriptions
@endsection


@section('breadcrumbs')

@endsection

@section('content')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    @if($plan->name == 'trial')
        <h4>Current Plan: You are on a free trial plan.</h4>
    @else
        <h4>Current Plan: {{ $plan->user_limit }} user with up to  {{ $plan->account_limit }} billed at @money($plan->price, $plan->currency) a {{ $plan->frequency }}.</h4>
    @endif
        <h3>New Plan: <span id="newplan"></span></h3>
        <form method="POST" action="/user/subscription/payment">
            @csrf

            @if($account->currency === null)
                <div class="wrapper">
                    <div class="toggle_radio">
                        <input type="radio" class="toggle_option" id="first_toggle" name="currency" value="USD">
                        <input type="radio" checked class="toggle_option" id="second_toggle" name="currency" value="GBP">
                        <input type="radio" class="toggle_option" id="third_toggle" name="currency" value="EUR">
                        <label for="first_toggle"><p>USD</p></label>
                        <label for="second_toggle"><p>GBP</p></label>
                        <label for="third_toggle"><p>EUR</p></label>
                        <div class="toggle_option_slider"></div>
                    </div>
                </div>
            @else
                <input style="visibility: hidden" type="radio" checked name="currency" value="{{ $account->currency }}">
            @endif

        <div class="wrapper">
            <div class="toggle_accounts">
                <input type="radio" checked class="toggle_accounts" id="toggle_accounts_1" name="accounts" value="1">
                <input type="radio" @if($plan->account_limit == 5) checked @endif class="toggle_accounts" id="toggle_accounts_2" name="accounts" value="5">
                <input type="radio" @if($plan->account_limit == 10) checked @endif class="toggle_accounts" id="toggle_accounts_3" name="accounts" value="10">
                <input type="radio" @if($plan->account_limit == 20) checked @endif class="toggle_accounts" id="toggle_accounts_4" name="accounts" value="20">
                <input type="radio" @if($plan->account_limit == 50) checked @endif class="toggle_accounts" id="toggle_accounts_5" name="accounts" value="50">
                <input type="radio" @if($plan->account_limit == 51) checked @endif class="toggle_accounts" id="toggle_accounts_6" name="accounts" value="51">
                <label for="toggle_accounts_1"><p>1</p></label>
                <label for="toggle_accounts_2"><p>5</p></label>
                <label for="toggle_accounts_3"><p>10</p></label>
                <label for="toggle_accounts_4"><p>20</p></label>
                <label for="toggle_accounts_5"><p>50</p></label>
                <label for="toggle_accounts_6"><p>50+</p></label>
                <div class="toggle_accounts_slider"></div>
            </div>
        </div>

        <div class="form-check">
            <button class="btn btn-primary" type="submit">Select Plan</button>
        </div>
    </form>

    <div id="app"></div>

@endsection

<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous">
</script>

<script>
    $(function(){
        $('input:radio').change(function() {
            getPlan();
        });

        function getPlan() {
            let currency = $("input[name='currency']:checked").val();
            let accounts = $("input[name='accounts']:checked").val();

            axios.post('/api/plan', {
                currency: currency,
                accounts: accounts,
            })
                .then(function (response) {
                    $('#newplan').text(response.data.price / 100 + ' ' + response.data.currency );
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    });
</script>
<style>
    @import url(http://fonts.googleapis.com/css?family=Source+Sans+Pro);
    *{
        margin:0px;
        padding:0px;
    }

    .wrapper {
        margin: 40px 0;
    }
    .toggle_radio{
        position: relative;
        background: #497dd0;

        margin: 4px auto;
        overflow: hidden;
        padding: 0 !important;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        position: relative;
        height: 26px;
        width: 318px;
    }
    .toggle_radio > * {
        float: left;
    }
    .toggle_radio input[type=radio]{
        display: none;
        /*position: fixed;*/
    }
    .toggle_radio label{
        font: 90%/1.618 "Source Sans Pro";
        color: rgba(255,255,255,.9);
        z-index: 0;
        display: block;
        width: 100px;
        height: 20px;
        margin: 3px 3px;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        cursor: pointer;
        z-index: 1;
        text-align: center;
    }
    .toggle_option_slider{
        width: 100px;
        height: 20px;
        position: absolute;
        top: 3px;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        -webkit-transition: all .4s ease;
        -moz-transition: all .4s ease;
        -o-transition: all .4s ease;
        -ms-transition: all .4s ease;
        transition: all .4s ease;
    }

    #first_toggle:checked ~ .toggle_option_slider{
        background: rgba(255,255,255,.3);
        left: 3px;
    }
    #second_toggle:checked ~ .toggle_option_slider{
        background: rgba(255,255,255,.3);
        left: 109px;
    }
    #third_toggle:checked ~ .toggle_option_slider{
        background: rgba(255,255,255,.3);
        left: 215px;
    }


/*Users*/
    .toggle_accounts{
        position: relative;
        background: #497dd0;

        margin: 4px auto;
        overflow: hidden;
        padding: 0 !important;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        position: relative;
        height: 26px;
        width: 636px;
    }
    .toggle_accounts > * {
        float: left;
    }
    .toggle_accounts input[type=radio]{
        display: none;
        /*position: fixed;*/
    }
    .toggle_accounts label{
        font: 90%/1.618 "Source Sans Pro";
        color: rgba(255,255,255,.9);
        z-index: 0;
        display: block;
        width: 100px;
        height: 20px;
        margin: 3px 3px;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        cursor: pointer;
        z-index: 1;
        text-align: center;

    }
    .toggle_accounts_slider{
        width: 100px;
        height: 20px;
        position: absolute;
        top: 3px;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        -webkit-transition: all .4s ease;
        -moz-transition: all .4s ease;
        -o-transition: all .4s ease;
        -ms-transition: all .4s ease;
        transition: all .4s ease;
    }
    #toggle_accounts_1:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 3px;
    }
    #toggle_accounts_2:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 109px;
    }
    #toggle_accounts_3:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 215px;
    }
    #toggle_accounts_4:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 321px;
    }
    #toggle_accounts_5:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 427px;
    }
    #toggle_accounts_6:checked ~ .toggle_accounts_slider{
        background: rgba(255,255,255,.3);
        left: 533px;
    }

</style>


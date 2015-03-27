<?
/**
 * TEMPLATE_IS_NOT_SETTABLE
 */
?>
<?
#Helper::dd(Config::get('app.locale'));

$route = Route::currentRouteName();
#Helper::dd($page);

$room_type = Dic::whereSlugValues('room_type');
$room_types = array();
$room_type_prices = array();
if (@count($room_type))
    foreach ($room_type as $a => $arr) {

        if (is_object($arr->meta))
            $room_type[$a]->name = $arr->meta->name;
        if (is_object($arr->fields))
            foreach ($arr->fields as $field)
                $room_type[$a]->{$field->key} = $field->value;

        if ($arr->price) {
            $room_types[$arr->id . '.1'] = $arr->name . ', ' . trans("interface.rooms.single_occupancy");
            $room_type_prices[$arr->id . '.1'] = $arr->price;
        }
        if ($arr->price2) {
            $room_types[$arr->id . '.2'] = $arr->name . ', ' . trans("interface.rooms.double_occupancy");
            $room_type_prices[$arr->id . '.2'] = $arr->price2;
        }
    }

#Helper::d($room_types);
#Helper::dd($room_type_prices);

##$prices = json_encode($room_type->lists('price', 'id'));
##Helper::tad($room_type);
##Helper::dd($room_type->lists('name', 'id'));
?>
@if (0)
page:rooms = {{ URL::route('page', 'rooms') }}
@endif
        <div class="wrapper">
            <header class="main-header">
                @if ($route == 'mainpage')
                <h1 class="logo">
                    Sarykum hotel and spa
                </h1>
                @else
                <div class="logo">
                    <a href="{{ URL::route('mainpage') }}"></a>
                </div>
                @endif
                <nav>
                    <ul class="nav-ul">
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.rooms') }}" href="{{ URL::route('page', 'rooms') }}">
                                <span>{{ trans('interface.menu.rooms') }}
                            </a>
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.spa') }}" href="{{ URL::route('page', 'spa') }}">
                                <span>{{ trans('interface.menu.spa') }}
                            </a>
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.restaurant') }}" href="{{ URL::route('page', 'restaurant') }}">
                                <span>{{ trans('interface.menu.restaurant') }}
                            </a>
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.services') }}" href="{{ URL::route('page', 'services') }}">
                                <span>{{ trans('interface.menu.services') }}
                            </a>
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.discover') }}" href="{{ URL::route('page', 'discover') }}">
                                <span>{{ trans('interface.menu.discover') }}
                            </a>
                        <li class="nav-li">
                            <a data-hover="{{ trans('interface.menu.actions') }}" href="{{ URL::route('page', 'actions') }}">
                                <span>{{ trans('interface.menu.actions') }}
                            </a>
                    </ul>
                </nav>
                <div class="booking" id="bookBtn">
                    {{ trans('interface.menu.reserve') }} <span class="icon icon-angle-right"></span>
                    <div class="booking-form">
                        <div class="form-success">
                            {{ trans('interface.reserve.success') }}
                        </div>

                        {{ Form::open(array('url' => URL::route('ajax-reserve-room'), 'class' => 'smart-form', 'id' => 'reserve-form', 'role' => 'form', 'method' => 'POST')) }}
                            <fieldset class="date-data">
                                <section>
                                    <header>
                                        {{ trans('interface.reserve.select') }}
                                    </header>
                                    <div class="inline">
                                        {{ Form::select('room_type', $room_types) }}
                                    </div>
                                    <section class="datepickerFrom inline">
                                        {{ trans('interface.reserve.from') }}
                                        {{ Form::text('date_start', null, array('id' => 'datepickerFrom', 'class' => 'datepicker')) }}
                                    </section>
                                    <section class="datepickerTo inline">
                                        {{ trans('interface.reserve.to') }}
                                        {{ Form::text('date_stop', null, array('id' => 'datepickerTo', 'class' => 'datepicker_')) }}
                                    </section>
                                </section>
                            </fieldset>
                            <fieldset class="text-data">
                                <section>
                                    <header>
                                        {{ trans('interface.reserve.introduce') }}
                                    </header>
                                    <div class="inline">
                                        {{ Form::text('name', null, array('placeholder' => trans('interface.reserve.introduce'))) }}
                                    </div>
                                    <div class="inline">
                                        {{ Form::text('contact', null, array('placeholder' => trans('interface.reserve.contact'))) }}
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    @if (0)
                                    <div class="price inline">
                                        {{ trans('interface.reserve.daily_price') }} <span>4500 rub.</span>
                                    </div>
                                    @endif
                                    <button type="submit" class="btn bordered inline">
                                        {{ trans('interface.reserve.reserve') }}
                                    </button> 
                                </section>
                            </fieldset>                                                   

                        {{ Form::close() }}

                    </div>
                </div>
                <div class="phone">
                    <a href="#">
                        <span class="icon icon-phone"></span>
                        {{ trans('interface.phone_number') }}
                    </a>
                </div>
                <div class="lang">
                    {{ Helper::changeLocaleMenu() }}
                </div>                
            </header>

<script>
    var prices = {{ json_encode($room_type_prices) }};
    var currency = '{{ trans("interface.currency_short") }}';
</script>
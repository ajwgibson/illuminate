<?php

class BookingController extends BaseController {

	public function index()
	{
        $this->layout->with('subtitle', 'Bookings');

        $bookings = Booking::orderBy('last')->orderBy('first');

        $filtered = false;
		$filter_name           = Session::get('bookings_filter_name',       '');
        $filter_church         = Session::get('bookings_filter_church',     '');
        $filter_eventbrite     = Session::get('bookings_filter_eventbrite', '');
        $filter_registered     = Session::get('bookings_filter_registered', '');
        $filter_not_registered = Session::get('bookings_filter_not_registered', '');

        if (!(empty($filter_name))) {
            $bookings = $bookings
                ->where(function($query) use($filter_name) {
                    $query->where('bookings.first', 'LIKE', "%$filter_name%")
                          ->orWhere('bookings.last', 'LIKE', "%$filter_name%");
                });	
            $filtered = true;
        }

        if (!(empty($filter_church))) {
            $bookings = $bookings->where('source', 'Church');
            $filtered = true;
        }

        if (!(empty($filter_eventbrite))) {
            $bookings = $bookings->where('source', 'EventBrite');
            $filtered = true;
        }

        if (($filter_registered || $filter_not_registered) && !($filter_registered && $filter_not_registered)) {
            if ($filter_registered) {
                $ids = DB::table('booking_registration_counts')->whereRaw('tickets <= registrations')->lists('id');
                if ($ids) $bookings = $bookings->whereIn('id', $ids);
            } else {
                $ids = DB::table('booking_registration_counts')->whereRaw('tickets > registrations')->lists('id');
                if ($ids) $bookings = $bookings->whereIn('id', $ids);
            }
        }

		$bookings = $bookings->paginate(25);

		$this->layout->content = 
			View::make('bookings.index')
				->with('bookings', $bookings)
				->with('filtered', $filtered)
                ->with('filter_name', $filter_name)
                ->with('filter_church', $filter_church)
                ->with('filter_eventbrite', $filter_eventbrite)
                ->with('filter_registered', $filter_registered)
                ->with('filter_not_registered', $filter_not_registered);
	}

	/**
     * Changes the list filter values in the session
     * and redirects back to the index to force the filtered
     * list to be displayed.
     */
    public function filter()
    {
        $filter_name           = Input::get('filter_name');
        $filter_church         = Input::get('filter_church');
        $filter_eventbrite     = Input::get('filter_eventbrite');
        $filter_registered     = Input::get('filter_registered');
        $filter_not_registered = Input::get('filter_not_registered');
        
        Session::put('bookings_filter_name',           $filter_name);
        Session::put('bookings_filter_church',         $filter_church);
        Session::put('bookings_filter_eventbrite',     $filter_eventbrite);
        Session::put('bookings_filter_registered',     $filter_registered);
        Session::put('bookings_filter_not_registered', $filter_not_registered);

        return Redirect::route('bookings');
    }

	
	/**
     * Removes the list filter values from the session
     * and redirects back to the index to force the 
     * list to be displayed.
     */
    public function resetFilter()
    {
        if (Session::has('bookings_filter_name'))           Session::forget('bookings_filter_name');
        if (Session::has('bookings_filter_church'))         Session::forget('bookings_filter_church');
        if (Session::has('bookings_filter_eventbrite'))     Session::forget('bookings_filter_eventbrite');
        if (Session::has('bookings_filter_registered'))     Session::forget('bookings_filter_registered');
        if (Session::has('bookings_filter_not_registered')) Session::forget('bookings_filter_not_registered');

        return Redirect::route('bookings');
    }

}

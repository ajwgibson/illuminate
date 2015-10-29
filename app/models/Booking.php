<?php

class Booking extends Eloquent {
	
	protected $table = 'bookings';

	// Relationship: registrations
	public function registrations()
	{
		return $this->hasMany('Registration');
	}


	// How many delegates have been registered against this booking
    public function registration_count()
    {
    	if ($this->registrations) {
    		return $this->registrations->sum('tickets');
    	} else {
    		return 0;
    	}
    }

    // Have all the tickets on this booking been registered now?
    public function registered()
    {
        return $this->registration_count() >= $this->tickets;
    }

    // How many tickets on this booking are still to register?
    public function to_register()
    {
        return $this->tickets - $this->registration_count();
    }

    // Returns the name on the booking
    public function name()
    {
        return $this->first . ' ' . $this->last;
    }
}
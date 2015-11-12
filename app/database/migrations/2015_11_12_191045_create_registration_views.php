<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationViews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement(<<<'EOD1'
			create view registration_counts as
			  select 
			      booking_id, 
			      count(booking_id) as registered 
			    from registrations 
			    group by booking_id
EOD1
		);

		DB::statement(<<<'EOD2'
			create view booking_registration_counts as
			  select 
			    bookings.id,
			    bookings.first,
			    bookings.last,
			    bookings.tickets, 
			    coalesce(registration_counts.registered, 0) as registrations
			  from bookings left join registration_counts on bookings.id=registration_counts.booking_id
EOD2
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('DROP VIEW booking_registration_counts');
		DB::statement('DROP VIEW registration_counts');
	}

}

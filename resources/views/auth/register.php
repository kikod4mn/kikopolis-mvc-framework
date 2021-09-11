@merge(layouts.main)
{{ @title(Register A New Account) }}

<h1>Register</h1>

<form method="post" action="/register">
	<div class="mb-3 form-group">
		<label for="email" class="form-label">Email</label>
		<input type="email" name="email" class="form-control" id="email">
	</div>
	<div class="mb-3 form-group">
		<label for="first_name" class="form-label">First Name</label>
		<input type="text" name="first_name" class="form-control" id="first_name">
	</div>
	<div class="mb-3 form-group">
		<label for="last_name" class="form-label">Last Name</label>
		<input type="text" name="last_name" class="form-control" id="last_name">
	</div>
	<div class="mb-3 form-group">
		<label for="password" class="form-label">Password</label>
		<input type="password" name="password" class="form-control" id="password">
	</div>
	<div class="mb-3 form-group">
		<label for="password_repeat" class="form-label">Repeat Password</label>
		<input type="password" name="password_repeat" class="form-control" id="password_repeat">
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
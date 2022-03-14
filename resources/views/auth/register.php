@merge(layouts.auth)
{{ @title(Register A New Account) }}

<h1>Register</h1>

<form method="post" action="/register">
	<div class="row">
		<div class="col">
			<div class="mb-3 form-group">
				<label for="firstName" class="form-label">First Name</label>
				<input type="text" name="firstName" class="form-control" id="firstName">
			</div>
		</div>
		<div class="col">
			<div class="mb-3 form-group">
				<label for="lastName" class="form-label">Last Name</label>
				<input type="text" name="lastName" class="form-control" id="lastName">
			</div>
		</div>
	</div>
	<div class="mb-3 form-group">
		<label for="email" class="form-label">Email</label>
		<input type="email" name="email" class="form-control" id="email">
	</div>
	<div class="mb-3 form-group">
		<label for="password" class="form-label">Password</label>
		<input type="password" name="password" class="form-control" id="password">
	</div>
	<div class="mb-3 form-group">
		<label for="passwordRepeat" class="form-label">Repeat Password</label>
		<input type="password" name="passwordRepeat" class="form-control" id="passwordRepeat">
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
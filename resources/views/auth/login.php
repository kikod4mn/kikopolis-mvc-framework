@merge(layouts.main)
{{ @title(Login Page) }}

<h1>Sign in to the app</h1>

<form method="post" action="/login">
	<div class="mb-3 form-group">
		<label for="email" class="form-label">Email</label>
		<input type="email" name="email" class="form-control" id="email">
	</div>
	<div class="mb-3 form-group">
		<label for="password" class="form-label">Password</label>
		<input type="password" name="password" class="form-control" id="password">
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
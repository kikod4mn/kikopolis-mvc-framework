@merge(layouts.main)
{{ @title(Contact us) }}

<h1>Contact us!</h1>

<form method="post" action="/contact">
	<div class="mb-3 form-group">
		<label for="email" class="form-label">Email</label>
		<input type="email" name="email" class="form-control" id="email">
	</div>
	<div class="mb-3 form-group">
		<label for="subject" class="form-label">Subject</label>
		<input type="text" name="subject" class="form-control" id="subject">
	</div>
	<div class="mb-3 form-group">
		<label for="message" class="form-label">Message</label>
		<textarea name="message" class="form-control" id="message"></textarea>
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<div class="auth-container">
	<!-- Left Column: Forgot Password Form -->
	<div class="auth-form-column">
		<h2>Forgot Password?</h2>

		<?php if (!empty($errors['general'])): ?>
			<div class="auth-general-error">
				<?= htmlspecialchars($errors['general']) ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="/auth/forgot-password">
			<?= \Core\CSRF::field() ?>

			<div class="auth-form-group">
				<label for="email">Email Address:</label>
				<input
					type="email"
					id="email"
					name="email"
					value="<?= htmlspecialchars($old['email'] ?? '') ?>"
					autocomplete="email"
					required
					autofocus>
				<?php if (!empty($errors['email'])): ?>
					<div class="field-error">
						<?= htmlspecialchars($errors['email']) ?>
					</div>
				<?php endif; ?>
			</div>

			<button type="submit">
				Send Reset Link
			</button>
		</form>
	</div>
</div>
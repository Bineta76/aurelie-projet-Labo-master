
  <form action="inscription.php" method="POST">
    <label for="username">Nom</label>
    <input type="text" id="username" name="username" required>

    <label for="prenom">Prénom</label>
    <input type="text" id="prenom" name="prenom" required>

    <label for="login">Login</label>
    <input type="text" id="login" name="login" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required>

    <input type="submit" value="S'inscrire">
  </form>
</div>

<script>
  setTimeout(() => {
    const formContainer = document.getElementById("tempText");
    if(formContainer) {
      formContainer.style.opacity = 1;
    }
  }, 100); // Affiche le formulaire après 100ms
</script>

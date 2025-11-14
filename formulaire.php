form action="inscription.php" method="POST">
    <label for="username">Nom</label>
    <input type="text" id="username" name="username" required>

    <label for="Prenom">Prénom</label>
    <input type="text" id="Prenom" name="Prenom" required>

    <label for="Login">Login</label>
    <input type="text" id="Login" name="Login" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required>

    <input type="submit" value="S'inscrire">
  </form>
</div>

<script>
  setTimeout(() => {
    document.getElementById("tempText").style.opacity = 1;
  }, 100); // Affiche le formulaire après 100ms
</script>


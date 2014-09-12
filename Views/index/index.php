<form method="post" action="<?= Link::Url('user', 'login', array (), '.json?') ?>">
    <p>
        <label>Usuario </label>
        <input type="text" name="user" />
    </p>
    
    <p>
        <label>Clave </label>
        <input type="password" name="pass" />
    </p>
    
    <p>
        <input type="submit" />
    </p>
</form>

<form method="post" action="<?= Link::Url('user', 'cpw.json') ?>">
    <p>
        <label>viejo </label>
        <input type="text" name="old" />
    </p>
    
    <p>
        <label>nuevo </label>
        <input type="text" name="new" />
    </p>
    
    <p>
        <input type="submit" />
    </p>
</form>
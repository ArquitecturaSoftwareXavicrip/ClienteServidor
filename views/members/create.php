<?php
/**
 * Vista: Crear Miembro
 * 
 * CLIENTE: Formulario que captura datos del usuario y los envía al SERVIDOR
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<h2>Nuevo Miembro</h2>

<!-- CLIENTE: Formulario que envía datos al SERVIDOR mediante POST -->
<form method="POST" action="/index.php?controller=member&action=store" class="form">
    <div class="form-group">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" required 
               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
               class="<?php echo isset($errors['name']) ? 'error' : ''; ?>">
        <?php if (isset($errors['name'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['name']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required 
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
               class="<?php echo isset($errors['email']) ? 'error' : ''; ?>">
        <?php if (isset($errors['email'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="phone">Teléfono</label>
        <input type="tel" id="phone" name="phone" 
               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="registration_date">Fecha de Inscripción *</label>
        <input type="date" id="registration_date" name="registration_date" required 
               value="<?php echo htmlspecialchars($_POST['registration_date'] ?? date('Y-m-d')); ?>"
               class="<?php echo isset($errors['registration_date']) ? 'error' : ''; ?>">
        <?php if (isset($errors['registration_date'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['registration_date']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-actions">
        <!-- CLIENTE: Botón que envía datos al SERVIDOR -->
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/index.php?controller=member&action=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





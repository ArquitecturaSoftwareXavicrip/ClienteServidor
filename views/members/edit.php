<?php
/**
 * Vista: Editar Miembro
 * 
 * CLIENTE: Formulario que muestra datos del SERVIDOR y permite editarlos
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<h2>Editar Miembro</h2>

<!-- CLIENTE: Formulario que envía datos actualizados al SERVIDOR -->
<form method="POST" action="/index.php?controller=member&action=update" class="form">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($member['id']); ?>">
    
    <div class="form-group">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" required 
               value="<?php echo htmlspecialchars($member['name']); ?>"
               class="<?php echo isset($errors['name']) ? 'error' : ''; ?>">
        <?php if (isset($errors['name'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['name']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required 
               value="<?php echo htmlspecialchars($member['email']); ?>"
               class="<?php echo isset($errors['email']) ? 'error' : ''; ?>">
        <?php if (isset($errors['email'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="phone">Teléfono</label>
        <input type="tel" id="phone" name="phone" 
               value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="registration_date">Fecha de Inscripción *</label>
        <input type="date" id="registration_date" name="registration_date" required 
               value="<?php echo htmlspecialchars($member['registration_date']); ?>"
               class="<?php echo isset($errors['registration_date']) ? 'error' : ''; ?>">
        <?php if (isset($errors['registration_date'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['registration_date']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="status">Estado *</label>
        <select id="status" name="status" required>
            <option value="active" <?php echo $member['status'] === 'active' ? 'selected' : ''; ?>>Activo</option>
            <option value="inactive" <?php echo $member['status'] === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
            <option value="suspended" <?php echo $member['status'] === 'suspended' ? 'selected' : ''; ?>>Suspendido</option>
        </select>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="/index.php?controller=member&action=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





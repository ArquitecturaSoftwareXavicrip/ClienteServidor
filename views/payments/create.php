<?php
/**
 * Vista: Crear Pago
 * 
 * CLIENTE: Formulario para registrar nuevo pago
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<h2>Registrar Nuevo Pago</h2>

<form method="POST" action="/index.php?controller=payment&action=store" class="form">
    <div class="form-group">
        <label for="member_id">Miembro *</label>
        <select id="member_id" name="member_id" required 
                class="<?php echo isset($errors['member_id']) ? 'error' : ''; ?>">
            <option value="">Seleccione un miembro</option>
            <?php foreach ($members as $member): ?>
                <option value="<?php echo $member['id']; ?>" 
                        <?php echo (isset($_POST['member_id']) && $_POST['member_id'] == $member['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($member['name'] . ' - ' . $member['email']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['member_id'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['member_id']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="membership_type_id">Tipo de Membresía *</label>
        <select id="membership_type_id" name="membership_type_id" required 
                class="<?php echo isset($errors['membership_type_id']) ? 'error' : ''; ?>">
            <option value="">Seleccione un tipo</option>
            <?php foreach ($membershipTypes as $type): ?>
                <option value="<?php echo $type['id']; ?>" 
                        data-price="<?php echo $type['price']; ?>"
                        <?php echo (isset($_POST['membership_type_id']) && $_POST['membership_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type['name'] . ' - $' . number_format($type['price'], 2)); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['membership_type_id'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['membership_type_id']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="amount">Monto *</label>
        <input type="number" id="amount" name="amount" step="0.01" min="0" required 
               value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>"
               class="<?php echo isset($errors['amount']) ? 'error' : ''; ?>">
        <?php if (isset($errors['amount'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['amount']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="payment_date">Fecha de Pago *</label>
        <input type="date" id="payment_date" name="payment_date" required 
               value="<?php echo htmlspecialchars($_POST['payment_date'] ?? date('Y-m-d')); ?>"
               class="<?php echo isset($errors['payment_date']) ? 'error' : ''; ?>">
        <?php if (isset($errors['payment_date'])): ?>
            <span class="error-message"><?php echo htmlspecialchars($errors['payment_date']); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="payment_method">Método de Pago *</label>
        <select id="payment_method" name="payment_method" required>
            <option value="cash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cash') ? 'selected' : ''; ?>>Efectivo</option>
            <option value="card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'card') ? 'selected' : ''; ?>>Tarjeta</option>
            <option value="transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'transfer') ? 'selected' : ''; ?>>Transferencia</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="notes">Notas</label>
        <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Registrar Pago</button>
        <a href="/index.php?controller=payment&action=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





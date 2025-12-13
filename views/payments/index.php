<?php
/**
 * Vista: Lista de Pagos
 * 
 * CLIENTE: Muestra los pagos recibidos del SERVIDOR
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<h2>Gestión de Pagos</h2>

<div class="actions">
    <a href="/index.php?controller=payment&action=create" class="btn btn-primary">➕ Registrar Pago</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Miembro</th>
            <th>Email</th>
            <th>Tipo Membresía</th>
            <th>Monto</th>
            <th>Fecha Pago</th>
            <th>Método</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($payments)): ?>
            <tr>
                <td colspan="7" class="text-center">No hay pagos registrados</td>
            </tr>
        <?php else: ?>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['id']); ?></td>
                    <td><?php echo htmlspecialchars($payment['member_name']); ?></td>
                    <td><?php echo htmlspecialchars($payment['member_email']); ?></td>
                    <td><?php echo htmlspecialchars($payment['membership_type_name']); ?></td>
                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





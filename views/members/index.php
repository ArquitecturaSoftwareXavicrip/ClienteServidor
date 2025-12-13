<?php
/**
 * Vista: Lista de Miembros
 * 
 * CLIENTE: Esta vista se renderiza en el navegador del usuario.
 * Muestra los datos que el SERVIDOR envió después de procesar la petición.
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<h2>Gestión de Miembros</h2>

<!-- CLIENTE: Botón que envía petición GET al SERVIDOR -->
<div class="actions">
    <a href="/index.php?controller=member&action=create" class="btn btn-primary">➕ Nuevo Miembro</a>
</div>

<!-- CLIENTE: Tabla que muestra datos recibidos del SERVIDOR -->
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Fecha Inscripción</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($members)): ?>
            <tr>
                <td colspan="7" class="text-center">No hay miembros registrados</td>
            </tr>
        <?php else: ?>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['id']); ?></td>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                    <td><?php echo htmlspecialchars($member['phone'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($member['registration_date']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $member['status'] === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($member['status']); ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <!-- CLIENTE: Enlaces que envían peticiones al SERVIDOR -->
                        <a href="/index.php?controller=member&action=edit&id=<?php echo $member['id']; ?>" class="btn btn-sm btn-secondary">Editar</a>
                        <a href="/index.php?controller=member&action=delete&id=<?php echo $member['id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('¿Está seguro de eliminar este miembro?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





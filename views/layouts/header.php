<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Gimnasio</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- CLIENTE: Esta es la interfaz que el usuario ve en su navegador -->
    <header class="header">
        <div class="container">
            <h1 class="logo">🏋️ Gimnasio MVC</h1>
            <nav class="nav">
                <a href="/index.php?controller=member&action=index" class="nav-link">Miembros</a>
                <a href="/index.php?controller=class&action=index" class="nav-link">Clases</a>
                <a href="/index.php?controller=payment&action=index" class="nav-link">Pagos</a>
            </nav>
        </div>
    </header>
    
    <main class="main container">
        <!-- Mensajes de éxito/error del SERVIDOR al CLIENTE -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                $messages = [
                    'created' => 'Registro creado exitosamente',
                    'updated' => 'Registro actualizado exitosamente',
                    'deleted' => 'Registro eliminado exitosamente'
                ];
                echo $messages[$_GET['success']] ?? 'Operación exitosa';
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php
                $messages = [
                    'not_found' => 'Registro no encontrado',
                    'delete_failed' => 'Error al eliminar el registro'
                ];
                echo $messages[$_GET['error']] ?? 'Ha ocurrido un error';
                ?>
            </div>
        <?php endif; ?>





<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
require_once __DIR__ . '/helpers/XSSProtection.php';

// Thêm security headers
XSSProtection::addSecurityHeaders();
$userModel = new UserModel();

$params = [];
if (!empty($_GET['keyword'])) {
    // Add length limit and additional validation
    $keyword = trim($_GET['keyword']);
    if (strlen($keyword) > 100) {
        $keyword = substr($keyword, 0, 100);
    }
    $params['keyword'] = XSSProtection::clean($keyword);
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
    <?php include 'views/header.php'?>
    <div class="container">
        <?php if (!empty($users)) {?>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Type</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) {?>
                        <tr>
                            <th scope="row"><?php echo XSSProtection::escape($user['id'])?></th>
                            <td>
                                <?php echo XSSProtection::escape($user['name'])?>
                            </td>
                            <td>
                                <?php echo XSSProtection::escape($user['fullname'])?>
                            </td>
                            <td>
                                <?php echo XSSProtection::escape($user['type'])?>
                            </td>
                            <td>
    <?php 
        $userId = filter_var($user['id'], FILTER_VALIDATE_INT);
        if ($userId):
    ?>
        <a href="form_user.php?id=<?php echo $userId ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-pencil-square-o" aria-hidden="true" title="Update"></i>
        </a>
        <a href="view_user.php?id=<?php echo $userId ?>" class="btn btn-sm btn-info">
            <i class="fa fa-eye" aria-hidden="true" title="View"></i>
        </a>
        <a href="delete_user.php?id=<?php echo $userId ?>" 
           onclick="return confirm('Are you sure?');"
           class="btn btn-sm btn-danger">
            <i class="fa fa-eraser" aria-hidden="true" title="Delete"></i>
        </a>
    <?php endif; ?>
</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else { ?>
            <div class="alert alert-info" role="alert">
                Không tìm thấy user nào<?php 
                if (!empty($params['keyword'])) {
                    echo ' với từ khóa: ' . XSSProtection::escape($params['keyword']);
                }
                ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>
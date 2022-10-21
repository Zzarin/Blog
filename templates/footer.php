</td>

<td width="300px" class="sidebar">
    <div class="sidebarHeader">Меню</div>
    <ul>
        <li><a href="/">Главная страница</a></li>
        <li><a href="/about">Обо мне</a></li>
        <li><a href="/articles/add">Добавить новую статью</a></li>
    </ul>
    <br><br><br>
    <?php if (isset($user)): ?>
        <?php if (($user->isAdmin()) && ($user->getNickname() === 'admin')): ?>
            <div><a href="/adminPanel/main">Перейти в панель администратора</a></div>
        <?php endif; ?>
    <?php endif; ?>
</td>
</tr>
<tr>
    <td class="footer" colspan="2">Все права защищены (c) Мой блог</td>
</tr>
</table>

</body>
</html>
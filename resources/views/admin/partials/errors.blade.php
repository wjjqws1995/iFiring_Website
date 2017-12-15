@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>沃喔窩!</strong>
        抱歉，您的输入有误！<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
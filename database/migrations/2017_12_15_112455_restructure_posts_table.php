<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestructurePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            //执行迁移的数据
            $table->string('subtitle')->after('title');//文章标题
            $table->renameColumn('content', 'content_raw');//Markdown格式文本
            $table->text('content_html')->after('content');//Markdown格式的HTML版本
            $table->string('page_image')->after('content_html');//文章缩略图
            $table->string('meta_description')->after('page_image');//文章备注
            $table->boolean('is_draft')->after('meta_description');//是否草稿
            $table->string('layout')->after('is_draft')->default('blog.layouts.post');//文章布局
        });
    }

    /**
     * Reverse the migrations. //迁移回滚；
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}

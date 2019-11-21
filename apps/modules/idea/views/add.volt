{% extends 'layout.volt' %}

{% block title %}Add New Idea{% endblock %}

{% block styles %}

{% endblock %}

{% block content %}

{{ flashSession.output() }}

{{ form('idea/add', 'method': 'POST') }}
    <div class="form-group" style="margin-top:100px">
        <label for='name'>Title : </label>
        {{ text_field('title', 'size': 50, 'class': "form-control") }}
    </div>

    <div class="form-group">
        <label for='name'>Description</label>
        {{ text_field('description', 'size': 100, 'class': "form-control") }}
    </div>

    <div class="form-group">
        <label for='name'>Author</label>
        {{ text_field('author_name', 'size': 50, 'class': "form-control") }}
    </div>

    <div class="form-group">
        <label for='name'>Email</label>
        {{ text_field('author_email', 'size': 50, 'class': "form-control") }}
    </div>

    {{ submit_button('Submit') }}

{{ end_form() }}

{% endblock %}

{% block scripts %}

{% endblock %}
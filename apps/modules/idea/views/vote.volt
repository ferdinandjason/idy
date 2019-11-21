{% extends 'layout.volt' %}

{% block title %}Vote Idea{% endblock %}

{% block styles %}

{% endblock %}

{% block content %}

{{ form('idea/rate', 'method': 'POST') }}
    <div class="form-group" style="margin-top:100px">
        <label for='name'>Rate : </label>
        {{ text_field('rate', 'size': 50, 'class': "form-control") }}
    </div>

    {{ text_field('id', 'type': 'hidden', 'value': idea_id, 'style' : 'display:none' ) }}
    {{ submit_button('Submit') }}

{{ end_form() }}

{% endblock %}

{% block scripts %}

{% endblock %}
<?php
/**
 * Template Name: Question Category
 * 
 * This template is used to display a list of questions for a specific category
 * and allow users to submit new questions.
 */

get_header();

$term = get_queried_object();
?>

<style>
    .question-category-page {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    .category-header {
        margin-bottom: 30px;
    }
    .category-header h1 {
        font-size: 28px;
        color: #333;
    }
    .ask-question-section {
        background-color: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 30px;
    }
    .ask-question-section h2 {
        font-size: 22px;
        margin-bottom: 15px;
    }
    #submit-question-form .form-group {
        margin-bottom: 15px;
    }
    #submit-question-form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    #submit-question-form input[type="text"],
    #submit-question-form textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    #submit-question-form textarea {
        height: 100px;
    }
    .submit-question {
        background-color: #0073aa;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    .submit-question:hover {
        background-color: #005177;
    }
    .question-list h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .qa-post {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .qa-post-title {
        font-size: 20px;
        margin-bottom: 10px;
    }
    .qa-post-title a {
        color: #0073aa;
        text-decoration: none;
    }
    .qa-post-title a:hover {
        text-decoration: underline;
    }
    .qa-post-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }
    .qa-post-excerpt {
        margin-bottom: 15px;
    }
    .qa-post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .qa-post-voting button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #666;
    }
    .qa-post-voting button:hover {
        color: #0073aa;
    }
    .qa-post-resolved {
        background-color: #4CAF50;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 14px;
    }
    .mark-resolved {
        background-color: #f0ad4e;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }
    .mark-resolved:hover {
        background-color: #ec971f;
    }
    #load-more-questions {
        display: block;
        width: 200px;
        margin: 20px auto;
        background-color: #0073aa;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    #load-more-questions:hover {
        background-color: #005177;
    }
</style>

<div class="question-category-page">
    <header class="category-header">
        <h1><?php echo esc_html($term->name); ?></h1>
        <p><?php echo esc_html($term->description); ?></p>
    </header>

    <section class="ask-question-section">
        <h2>Ask a Question</h2>
        <form id="submit-question-form">
            <?php wp_nonce_field('submit_question_nonce', 'submit_question_nonce'); ?>
            <input type="hidden" name="category" value="<?php echo esc_attr($term->term_id); ?>">
            <div class="form-group">
                <label for="question-title">Question Title</label>
                <input type="text" id="question-title" name="title" required>
            </div>
            <div class="form-group">
                <label for="question-content">Question Details</label>
                <textarea id="question-content" name="content" required></textarea>
            </div>
            <button type="submit" class="submit-question">Submit Question</button>
        </form>
    </section>

    <section class="question-list">
        <h2>Questions in <?php echo esc_html($term->name); ?></h2>
        <div id="questions-container">
            <?php
            $args = array(
                'post_type' => 'qa_post',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'question_category',
                        'field'    => 'term_id',
                        'terms'    => $term->term_id,
                    ),
                ),
                'posts_per_page' => 10,
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    get_template_part('template-parts/content', 'qa-post');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No questions found in this category.</p>';
            endif;
            ?>
        </div>
        <?php
        if ($query->max_num_pages > 1) :
            echo '<div class="load-more-container">';
            echo '<button id="load-more-questions" data-page="1" data-category="' . esc_attr($term->term_id) . '">Load More Questions</button>';
            echo '</div>';
        endif;
        ?>
    </section>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle question submission
    $('#submit-question-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=submit_question&nonce=' + qa_ajax.submit_nonce,
            success: function(response) {
                if (response.success) {
                    alert('Question submitted successfully!');
                    form[0].reset();
                    // Reload the question list
                    loadQuestions();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Handle voting
    $('#questions-container').on('click', '.upvote, .downvote', function() {
        var button = $(this);
        var postId = button.data('post-id');
        var voteType = button.hasClass('upvote') ? 'upvotes' : 'downvotes';

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'handle_vote',
                nonce: qa_ajax.vote_nonce,
                post_id: postId,
                vote_type: voteType
            },
            success: function(response) {
                if (response.success) {
                    // Update vote count
                    var countSpan = button.find('.vote-count');
                    var newCount = parseInt(countSpan.text()) + 1;
                    countSpan.text(newCount);
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Handle marking as resolved
    $('#questions-container').on('click', '.mark-resolved', function() {
        var button = $(this);
        var postId = button.data('post-id');

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mark_question_resolved',
                nonce: qa_ajax.resolve_nonce,
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    button.replaceWith('<span class="qa-post-resolved">Resolved</span>');
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Load more questions
    $('#load-more-questions').on('click', function() {
        var button = $(this);
        var page = button.data('page');
        var category = button.data('category');

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_questions',
                nonce: qa_ajax.submit_nonce,
                page: page,
                category: category
            },
            success: function(response) {
                if (response.success) {
                    $('#questions-container').append(response.data);
                    button.data('page', page + 1);
                    if (!response.data) {
                        button.remove(); // No more questions to load
                    }
                } else {
                    alert('Error loading questions: ' + response.data);
                }
            }
        });
    });

    // Function to load questions (for real-time updates)
    function loadQuestions() {
        var categoryId = $('input[name="category"]').val();
        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_questions',
                nonce: qa_ajax.submit_nonce,
                category_id: categoryId
            },
            success: function(response) {
                if (response.success) {
                    $('#questions-container').html(response.data);
                } else {
                    alert('Error loading questions: ' + response.data);
                }
            }
        });
    }
});
</script>

<?php get_footer(); ?>
document.addEventListener('DOMContentLoaded', function() {
    const upvoteBtn = document.getElementById('upvoteBtn');
    const downvoteBtn = document.getElementById('downvoteBtn');
    const voteCount = document.getElementById('voteCount');

    if (!upvoteBtn || !downvoteBtn || !voteCount) {
        console.log('Voting elements not found');
        return;
    }

    function handleVoteClick(button) {
        console.log('Vote button clicked:', button.getAttribute('data-vote-type'));

        // Disable both buttons during processing
        upvoteBtn.disabled = true;
        downvoteBtn.disabled = true;

        // Get the vote data
        const postId = button.getAttribute('data-post-id');
        const voteType = button.getAttribute('data-vote-type');

        // Create form data
        const formData = new URLSearchParams();
        formData.append('action', 'directory_vote');
        formData.append('nonce', directory_voting_ajax.nonce);
        formData.append('listing_id', postId);
        formData.append('vote_type', voteType);

        console.log('Sending vote request:', Object.fromEntries(formData));

        // Send the request
        fetch(directory_voting_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        })
        .then(response => {
            console.log('Raw response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update the vote count
                voteCount.textContent = data.data.score;
                
                // Update button states
                upvoteBtn.classList.remove('active');
                downvoteBtn.classList.remove('active');
                button.classList.add('active');

                // Optional: show success message
                console.log('Vote successful');
            } else {
                console.error('Vote failed:', data.data);
                alert('Error processing vote: ' + (data.data || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Vote error:', error);
            alert('Error processing vote. Please try again.');
        })
        .finally(() => {
            // Re-enable the buttons
            upvoteBtn.disabled = false;
            downvoteBtn.disabled = false;
        });
    }

    // Add click handlers to the buttons
    upvoteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        handleVoteClick(this);
    });

    downvoteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        handleVoteClick(this);
    });

    console.log('Voting system initialized');
});
<?php
/**
 * Template part for displaying blog pagination
 *
 * @package DuyDev
 */

// Set default values if not provided
$current_page = isset($current_page) ? $current_page : 1;
$total_pages = isset($total_pages) ? $total_pages : 1;

// Only show pagination if there are multiple pages
if ($total_pages > 1): ?>
<div class="blog-pagination" data-current-page="<?php echo $current_page; ?>" data-total-pages="<?php echo $total_pages; ?>">
    <div class="pagination-wrapper">
        <?php if ($current_page > 1): ?>
            <button class="pagination-btn pagination-prev" data-page="<?php echo $current_page - 1; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <g opacity="1">
                    <path d="M15.5302 18.9693C15.5999 19.039 15.6552 19.1217 15.6929 19.2128C15.7306 19.3038 15.75 19.4014 15.75 19.4999C15.75 19.5985 15.7306 19.6961 15.6929 19.7871C15.6552 19.8781 15.5999 19.9609 15.5302 20.0306C15.4606 20.1002 15.3778 20.1555 15.2868 20.1932C15.1957 20.2309 15.0982 20.2503 14.9996 20.2503C14.9011 20.2503 14.8035 20.2309 14.7124 20.1932C14.6214 20.1555 14.5387 20.1002 14.469 20.0306L6.96899 12.5306C6.89926 12.4609 6.84394 12.3782 6.80619 12.2871C6.76845 12.1961 6.74902 12.0985 6.74902 11.9999C6.74902 11.9014 6.76845 11.8038 6.80619 11.7127C6.84394 11.6217 6.89926 11.539 6.96899 11.4693L14.469 3.9693C14.6097 3.82857 14.8006 3.74951 14.9996 3.74951C15.1986 3.74951 15.3895 3.82857 15.5302 3.9693C15.671 4.11003 15.75 4.30091 15.75 4.49993C15.75 4.69895 15.671 4.88982 15.5302 5.03055L8.55993 11.9999L15.5302 18.9693Z" fill="#111921"/>
                    </g>
                </svg>
            </button>
        <?php endif; ?>
        
        <div class="pagination-numbers">
            <?php
            // Calculate pagination range
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            // Always show first page
            if ($start_page > 1):
            ?>
                <button class="pagination-number <?php echo $current_page == 1 ? 'active' : ''; ?>" data-page="1">1</button>
                <?php if ($start_page > 2): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <button class="pagination-number <?php echo $current_page == $i ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
            <?php endfor; ?>
            
            <?php 
            // Always show last page
            if ($end_page < $total_pages): 
            ?>
                <?php if ($end_page < $total_pages - 1): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
                <button class="pagination-number <?php echo $current_page == $total_pages ? 'active' : ''; ?>" data-page="<?php echo $total_pages; ?>"><?php echo $total_pages; ?></button>
            <?php endif; ?>
        </div>
        
        <?php if ($current_page < $total_pages): ?>
            <button class="pagination-btn pagination-next" data-page="<?php echo $current_page + 1; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M17.031 12.5306L9.53104 20.0306C9.46136 20.1002 9.37863 20.1555 9.28759 20.1932C9.19654 20.2309 9.09896 20.2503 9.00042 20.2503C8.90187 20.2503 8.80429 20.2309 8.71324 20.1932C8.6222 20.1555 8.53947 20.1002 8.46979 20.0306C8.40011 19.9609 8.34483 19.8781 8.30712 19.7871C8.26941 19.6961 8.25 19.5985 8.25 19.4999C8.25 19.4014 8.26941 19.3038 8.30712 19.2128C8.34483 19.1217 8.40011 19.039 8.46979 18.9693L15.4401 11.9999L8.46979 5.03055C8.32906 4.88982 8.25 4.69895 8.25 4.49993C8.25 4.30091 8.32906 4.11003 8.46979 3.9693C8.61052 3.82857 8.80139 3.74951 9.00042 3.74951C9.19944 3.74951 9.39031 3.82857 9.53104 3.9693L17.031 11.4693C17.1008 11.539 17.1561 11.6217 17.1938 11.7127C17.2316 11.8038 17.251 11.9014 17.251 11.9999C17.251 12.0985 17.2316 12.1961 17.1938 12.2871C17.1561 12.3782 17.1008 12.4609 17.031 12.5306Z" fill="#111921"/>
                </svg>
            </button>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?> 
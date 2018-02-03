
      <article class="content forms-page">
                    <div class="title-block">
                        <h3 class="title">CartFuel</h3>
                        <p class="title-description"> Install App </p>
                    </div>
                    <div class="subtitle-block">
                        <h3 class="subtitle"> Enter your shop domain to log in or install this app.</h3>
                    </div>
                    <section class="section">
                        <div class="row sameheight-container">
                            <div class="col-md-6">
                                <div class="card card-block sameheight-item" style="height: 710px;">
                                  
                                    <form role="form" action="<?php echo base_url(); ?>Auth/check_login" method="get">
                                        <div class="form-group">
                                            <input class="form-control underlined" type="text" placeholder="example.myshopify.com" name="shop"> 
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Install</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                           
                        </div>
                    </section>
                    
                </article>              

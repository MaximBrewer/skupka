<div class="form-horizontal">
    <div class="control-group">
        <label class="control-label with-tip" for="#{prefix}_ddlAMPType" title="#{messages.contentTypeTip}">#{messages.contentType}</label>
        <div class="controls">
            <select id="#{prefix}_ddlAMPType" v-model="layout.contentType.type">
                <option value="inline">#{messages.types.inline}</option>
                <option value="url">#{messages.types.url}</option>
                <option value="gallery">#{messages.types.gallery}</option>
                <option value="youtube">#{messages.types.youtube}</option>
                <option value="vimeo">#{messages.types.vimeo}</option>
            </select>
        </div>
    </div>

    <div id="#{prefix}_amp_section_inline" class="amp-type-section" v-show="layout.contentType.type == 'inline'">
        <div class="control-group">
            <label class="control-label with-tip" title="#{messages.linkTypeTip}">#{messages.linkTypeLbl}</label>
            <div class="controls">
                <div class="btn-group btn-group-switcher">
                    <label class="btn btn-default" for="#{prefix}_inline_btnLinkText" v-bind:class="{'active': layout.contentType.inline.linkType == 'text'}">
                        #{messages.linkType.text}
                        <input type="radio" id="#{prefix}_inline_btnLinkText" name="#{prefix}_inline_linkType" value="text" v-model="layout.contentType.inline.linkType" />
                    </label>
                    <label class="btn btn-default" for="#{prefix}_inline_btnLinkImage" v-bind:class="{'active': layout.contentType.inline.linkType == 'image'}">
                        #{messages.linkType.image}
                        <input type="radio" id="#{prefix}_inline_btnLinkImage" name="#{prefix}_inline_linkType" value="image" v-model="layout.contentType.inline.linkType" />
                    </label>
                </div>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_inline_linkTextSection" v-show="layout.contentType.inline.linkType == 'text'">
            <label class="control-label with-tip" for="#{prefix}_inline_linkText" title="#{messages.linkTextTip}">#{messages.linkText}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="3" id="#{prefix}_inline_linkText" v-model="layout.contentType.inline.text"></textarea>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_inline_linkImageSection" v-show="layout.contentType.inline.linkType == 'image'">
            <label class="control-label with-tip" for="#{prefix}_inline_linkImage" title="#{messages.linkImageTip}">#{messages.linkImage}</label>
            <div class="controls">
                <div class="jeditor-image-holder"></div>
                <span class="input-append">
                    <input class="input-medium jeditor-imagepath-ctrl" type="text" id="#{prefix}_inline_linkImage" value="" readonly="readonly" v-model="layout.contentType.inline.image" />
                    <a class="btn btn-default jeditor-btn-select-image" href="#" data-ref-id="#{prefix}_inline_linkImage"><i class="icon-edit"></i> #{messages.select}</a>
                    <a href="#" title="" class="btn btn-default jeditor-btn-clear-image with-tip" data-ref-id="#{prefix}_inline_linkImage" data-original-title="#{messages.remove}"><i class="icon-remove"></i></a>
                </span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label with-tip" for="#{prefix}_tbxAMPInlineContent" title="#{messages.contentTip}">#{messages.content}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="5" id="#{prefix}_tbxAMPInlineContent" v-model="layout.contentType.inline.content"></textarea>
            </div>
        </div>
    </div>

    <div id="#{prefix}_amp_section_url" class="amp-type-section" v-show="layout.contentType.type == 'url'">
        <div class="control-group">
            <label class="control-label with-tip" title="#{messages.linkTypeTip}">#{messages.linkTypeLbl}</label>
            <div class="controls">
                <div class="btn-group btn-group-switcher">
                    <label class="btn btn-default" for="#{prefix}_url_btnLinkText" v-bind:class="{'active': layout.contentType.url.linkType == 'text'}">
                        #{messages.linkType.text}
                        <input type="radio" id="#{prefix}_url_btnLinkText" name="#{prefix}_url_linkType" value="text" v-model="layout.contentType.url.linkType" />
                    </label>
                    <label class="btn btn-default" for="#{prefix}_url_btnLinkImage" v-bind:class="{'active': layout.contentType.url.linkType == 'image'}">
                        #{messages.linkType.image}
                        <input type="radio" id="#{prefix}_url_btnLinkImage" name="#{prefix}_url_linkType" value="image" v-model="layout.contentType.url.linkType" />
                    </label>
                </div>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_url_linkTextSection" v-show="layout.contentType.url.linkType == 'text'">
            <label class="control-label with-tip" for="#{prefix}_url_linkText" title="#{messages.linkTextTip}">#{messages.linkText}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="3" id="#{prefix}_url_linkText" v-model="layout.contentType.url.text"></textarea>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_url_linkImageSection" v-show="layout.contentType.url.linkType == 'image'">
            <label class="control-label with-tip" for="#{prefix}_url_linkImage" title="#{messages.linkImageTip}">#{messages.linkImage}</label>
            <div class="controls">
                <div class="jeditor-image-holder"></div>
                <span class="input-append">
                    <input class="input-medium jeditor-imagepath-ctrl" type="text" id="#{prefix}_url_linkImage" value="" readonly="readonly" v-model="layout.contentType.url.image" />
                    <a class="btn btn-default jeditor-btn-select-image" href="#" data-ref-id="#{prefix}_url_linkImage"><i class="icon-edit"></i> #{messages.select}</a>
                    <a href="#" title="" class="btn btn-default jeditor-btn-clear-image with-tip" data-ref-id="#{prefix}_url_linkImage" data-original-title="#{messages.remove}"><i class="icon-remove"></i></a>
                </span>
            </div>
        </div>

        <div id="#{prefix}_urls" class="ari-cloner-container" data-cloner-control-key="urls" data-cloner-opt-items="1">
            <div class="text-right">
                <a href="#" class="btn btn-success ari-cloner-add-item with-tip" title="#{messages.itemAdd}" data-tooltip-placement="top"><i class="icon-plus"></i> #{messages.itemAdd}</a>
            </div>
            <div class="ari-cloner-items">
                <div class="ari-cloner-template control-group">
                    <label class="control-label with-tip-lazy" title="#{messages.urlTip}">#{messages.url}</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" placeholder="#{messages.urlPlaceholder}" data-cloner-control-key="url" />
                        <a href="#" class="btn btn-mini ari-cloner-remove-item with-tip-lazy" title="#{messages.itemRemove}" data-tooltip-placement="top"><i class="icon-remove"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-moveup-item with-tip-lazy" title="#{messages.itemUp}" data-tooltip-placement="top"><i class="icon-arrow-up"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-movedown-item with-tip-lazy" title="#{messages.itemDown}" data-tooltip-placement="top"><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="#{prefix}_amp_section_gallery" class="amp-type-section" v-show="layout.contentType.type == 'gallery'">
        <div class="control-group">
            <label class="control-label with-tip" title="#{messages.linkTypeTip}">#{messages.layoutTypeLbl}</label>
            <div class="controls">
                <div class="btn-group btn-group-switcher">
                    <label class="btn active" for="#{prefix}_gallery_btnLinkText" v-bind:class="{'active': layout.contentType.gallery.layout == 'text'}">
                        #{messages.linkType.text}
                        <input type="radio" id="#{prefix}_gallery_btnLinkText" name="#{prefix}_gallery_layoutType" value="text" v-model="layout.contentType.gallery.layout" />
                    </label>
                    <label class="btn" for="#{prefix}_gallery_btnLinkImage" v-bind:class="{'active': layout.contentType.gallery.layout == 'image'}">
                        #{messages.linkType.image}
                        <input type="radio" id="#{prefix}_gallery_btnLinkImage" name="#{prefix}_gallery_layoutType" value="image" v-model="layout.contentType.gallery.layout" />
                    </label>
                    <label class="btn" for="#{prefix}_gallery_btnLinkGallery" v-bind:class="{'active': layout.contentType.gallery.layout == 'gallery'}">
                        #{messages.layoutType.gallery}
                        <input type="radio" id="#{prefix}_gallery_btnLinkGallery" name="#{prefix}_gallery_layoutType" value="gallery" v-model="layout.contentType.gallery.layout" />
                    </label>
                </div>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_gallery_linkTextSection" v-show="layout.contentType.gallery.layout == 'text'">
            <label class="control-label with-tip" for="#{prefix}_gallery_linkText" title="#{messages.linkTextTip}">#{messages.linkText}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="3" id="#{prefix}_gallery_linkText" v-model="layout.contentType.gallery.text"></textarea>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_gallery_linkImageSection" v-show="layout.contentType.gallery.layout == 'image'">
            <label class="control-label with-tip" for="#{prefix}_gallery_linkImage" title="#{messages.linkImageTip}">#{messages.linkImage}</label>
            <div class="controls">
                <div class="jeditor-image-holder"></div>
            <span class="input-append">
                <input class="input-medium jeditor-imagepath-ctrl" type="text" id="#{prefix}_gallery_linkImage" value="" readonly="readonly" v-model="layout.contentType.gallery.image" />
                <a class="btn btn-default jeditor-btn-select-image" href="#" data-ref-id="#{prefix}_gallery_linkImage"><i class="icon-edit"></i> #{messages.select}</a>
                <a href="#" title="" class="btn btn-default jeditor-btn-clear-image with-tip" data-ref-id="#{prefix}_gallery_linkImage" data-original-title="#{messages.remove}"><i class="icon-remove"></i></a>
            </span>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_gallery_linkGallerySection" v-show="layout.contentType.gallery.layout == 'gallery'">
        </div>

        <div id="#{prefix}_folders" class="ari-cloner-container" data-cloner-control-key="folders" data-cloner-opt-items="1">
            <div class="text-right">
                <a href="#" class="btn btn-success ari-cloner-add-item with-tip" title="#{messages.itemAdd}" data-tooltip-placement="top"><i class="icon-plus"></i> #{messages.itemAdd}</a>
            </div>
            <div class="ari-cloner-items">
                <div class="ari-cloner-template control-group">
                    <label class="control-label with-tip-lazy" title="#{messages.imageFolderTip}">#{messages.imageFolder}</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge with-tip-lazy" placeholder="#{messages.imageFolderPlaceholder}" data-cloner-control-key="folder" />
                        <a href="#" class="btn btn-mini ari-cloner-remove-item with-tip-lazy" title="#{messages.itemRemove}" data-tooltip-placement="top"><i class="icon-remove"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-moveup-item with-tip-lazy" title="#{messages.itemUp}" data-tooltip-placement="top"><i class="icon-arrow-up"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-movedown-item with-tip-lazy" title="#{messages.itemDown}" data-tooltip-placement="top"><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="#{prefix}_amp_section_youtube" class="amp-type-section" v-show="layout.contentType.type == 'youtube'">
        <div class="control-group">
            <label class="control-label with-tip" title="#{messages.linkTypeTip}">#{messages.linkTypeLbl}</label>
            <div class="controls">
                <div class="btn-group btn-group-switcher">
                    <label class="btn active" for="#{prefix}_youtube_btnLinkText" v-bind:class="{'active': layout.contentType.youtube.layout == 'text'}">
                        #{messages.linkType.text}
                        <input type="radio" id="#{prefix}_youtube_btnLinkText" name="#{prefix}_youtube_layoutType" value="text" v-model="layout.contentType.youtube.layout" />
                    </label>
                    <label class="btn" for="#{prefix}_youtube_btnLinkImage" v-bind:class="{'active': layout.contentType.youtube.layout == 'image'}">
                        #{messages.linkType.image}
                        <input type="radio" id="#{prefix}_youtube_btnLinkImage" name="#{prefix}_youtube_layoutType" value="image" v-model="layout.contentType.youtube.layout" />
                    </label>
                </div>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_youtube_linkTextSection" v-show="layout.contentType.youtube.layout == 'text'">
            <label class="control-label with-tip" for="#{prefix}_youtube_linkText" title="#{messages.linkTextTip}">#{messages.linkText}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="3" id="#{prefix}_youtube_linkText" v-model="layout.contentType.youtube.text"></textarea>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_youtube_linkImageSection" v-show="layout.contentType.youtube.layout == 'image'">
            <label class="control-label with-tip" for="#{prefix}_youtube_linkImage" title="#{messages.linkImageTip}">#{messages.linkImage}</label>
            <div class="controls">
                <div class="jeditor-image-holder"></div>
                <span class="input-append">
                    <input class="input-medium jeditor-imagepath-ctrl" type="text" id="#{prefix}_youtube_linkImage" value="" readonly="readonly" v-model="layout.contentType.youtube.image" />
                    <a class="btn btn-default jeditor-btn-select-image" href="#" data-ref-id="#{prefix}_youtube_linkImage"><i class="icon-edit"></i> #{messages.select}</a>
                    <a href="#" title="" class="btn btn-default jeditor-btn-clear-image with-tip" data-ref-id="#{prefix}_youtube_linkImage" data-original-title="#{messages.remove}"><i class="icon-remove"></i></a>
                </span>
            </div>
        </div>

        <div id="#{prefix}_ytVideos" class="ari-cloner-container" data-cloner-control-key="ytVideos" data-cloner-opt-items="1">
            <div class="text-right">
                <a href="#" class="btn btn-success ari-cloner-add-item with-tip" title="#{messages.itemAdd}" data-tooltip-placement="top"><i class="icon-plus"></i> #{messages.itemAdd}</a>
            </div>
            <div class="ari-cloner-items">
                <div class="ari-cloner-template control-group">
                    <label class="control-label with-tip-lazy" title="#{messages.ytVideoTip}">#{messages.videoId}</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" placeholder="#{messages.ytVideoPlaceholder}" data-cloner-control-key="video" />
                        <a href="#" class="btn btn-mini ari-cloner-remove-item with-tip-lazy" title="#{messages.itemRemove}" data-tooltip-placement="top"><i class="icon-remove"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-moveup-item with-tip-lazy" title="#{messages.itemUp}" data-tooltip-placement="top"><i class="icon-arrow-up"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-movedown-item with-tip-lazy" title="#{messages.itemDown}" data-tooltip-placement="top"><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="#{prefix}_amp_section_vimeo" class="amp-type-section" v-show="layout.contentType.type == 'vimeo'">
        <div class="control-group">
            <label class="control-label with-tip" title="#{messages.linkTypeTip}">#{messages.linkTypeLbl}</label>
            <div class="controls">
                <div class="btn-group btn-group-switcher">
                    <label class="btn active" for="#{prefix}_vimeo_btnLinkText" v-bind:class="{'active': layout.contentType.vimeo.layout == 'text'}">
                        #{messages.linkType.text}
                        <input type="radio" id="#{prefix}_vimeo_btnLinkText" name="#{prefix}_vimeo_layoutType" value="text" v-model="layout.contentType.vimeo.layout" />
                    </label>
                    <label class="btn" for="#{prefix}_vimeo_btnLinkImage" v-bind:class="{'active': layout.contentType.vimeo.layout == 'image'}">
                        #{messages.linkType.image}
                        <input type="radio" id="#{prefix}_vimeo_btnLinkImage" name="#{prefix}_vimeo_layoutType" value="image" v-model="layout.contentType.vimeo.layout" />
                    </label>
                </div>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_vimeo_linkTextSection" v-show="layout.contentType.vimeo.layout == 'text'">
            <label class="control-label with-tip" for="#{prefix}_vimeo_linkText" title="#{messages.linkTextTip}">#{messages.linkText}</label>
            <div class="controls">
                <textarea class="input-xlarge" rows="3" id="#{prefix}_vimeo_linkText" v-model="layout.contentType.vimeo.text"></textarea>
            </div>
        </div>
        <div class="control-group" id="#{prefix}_vimeo_linkImageSection" v-show="layout.contentType.vimeo.layout == 'image'">
            <label class="control-label with-tip" for="#{prefix}_vimeo_linkImage" title="#{messages.linkImageTip}">#{messages.linkImage}</label>
            <div class="controls">
                <div class="jeditor-image-holder"></div>
                <span class="input-append">
                    <input class="input-medium jeditor-imagepath-ctrl" type="text" id="#{prefix}_vimeo_linkImage" value="" readonly="readonly" v-model="layout.contentType.vimeo.image" />
                    <a class="btn btn-default jeditor-btn-select-image" href="#" data-ref-id="#{prefix}_vimeo_linkImage"><i class="icon-edit"></i> #{messages.select}</a>
                    <a href="#" title="" class="btn btn-default jeditor-btn-clear-image with-tip" data-ref-id="#{prefix}_vimeo_linkImage" data-original-title="#{messages.remove}"><i class="icon-remove"></i></a>
                </span>
            </div>
        </div>

        <div id="#{prefix}_vimeoVideos" class="ari-cloner-container" data-cloner-control-key="vimeoVideos" data-cloner-opt-items="1">
            <div class="text-right">
                <a href="#" class="btn btn-success ari-cloner-add-item with-tip" title="#{messages.itemAdd}" data-tooltip-placement="top"><i class="icon-plus"></i> #{messages.itemAdd}</a>
            </div>
            <div class="ari-cloner-items">
                <div class="ari-cloner-template control-group">
                    <label class="control-label with-tip-lazy" title="#{messages.vimeoVideoTip}">#{messages.videoId}</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" placeholder="#{messages.vimeoVideoPlaceholder}" data-cloner-control-key="video" />
                        <a href="#" class="btn btn-mini ari-cloner-remove-item with-tip-lazy" title="#{messages.itemRemove}" data-tooltip-placement="top"><i class="icon-remove"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-moveup-item with-tip-lazy" title="#{messages.itemUp}" data-tooltip-placement="top"><i class="icon-arrow-up"></i></a>
                        <a href="#" class="btn btn-mini ari-cloner-movedown-item with-tip-lazy" title="#{messages.itemDown}" data-tooltip-placement="top"><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<blockquote>
    <i>#{messages.pluginUsage}</i>
</blockquote>
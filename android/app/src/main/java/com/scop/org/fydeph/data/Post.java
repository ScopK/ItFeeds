package com.scop.org.fydeph.data;

import java.util.ArrayList;
import java.util.Date;

public class Post {
	private String id;
	private Feed feed;
	private String title;
	private String description;
	private String link;
	private boolean unread;
	private boolean favorite;
	private Date date;
	private ArrayList<Tag> tags;
	
	
	public Post(String id,String title,String desc,String link,boolean unread,boolean favorite,Date date){
		this(id,title,desc);
		this.link = link;
		this.unread = unread;
		this.favorite = favorite;
		this.date = date;
	}
	
	public Post(String id,String title,String desc){
		this.id = id;
		this.title = title;
		this.description = desc;
		this.tags = new ArrayList<Tag>();
	}

	public Feed getFeed() {
		return feed;
	}
	public void setFeed(Feed feed) {
		this.feed = feed;
	}
	public String getLink() {
		return link;
	}
	public void setLink(String link) {
		this.link = link;
	}
	public boolean getUnread() {
		return unread;
	}
	public void setUnread(boolean unread) {
		this.unread = unread;
	}
	public boolean getFavorite() {
		return favorite;
	}
	public void setFavorite(boolean favorite) {
		this.favorite = favorite;
	}
	public Date getDate() {
		return date;
	}
	public void setDate(Date date) {
		this.date = date;
	}
	public ArrayList<Tag> getTags() {
		return tags;
	}
	public void setTags(ArrayList<Tag> tags) {
		this.tags = tags;
	}
	public void addTag(Tag tag) {
		this.tags.add(tag);
	}
	public String getId() {
		return id;
	}
	public String getTitle() {
		return title;
	}
	public String getDescription() {
		return description;
	}
}

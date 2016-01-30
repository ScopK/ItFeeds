package com.scop.org.itfeeds.data;

import java.util.ArrayList;

public class Feed implements MenuLabel {
	private String id;
	private String name;
	private ArrayList<Post> posts;
	private int unread;
	private int count;
	
	private Folder folder;
	private String link;
	private String rssLink;
	
	public Feed(String id,String name,String link,String rssLink,int unread,int count){
		this(id,name);
		this.link = link;
		this.rssLink = rssLink;
		this.unread = unread;
		this.count = count;
	}
	
	public Feed(String id,String name){
		this.id = id;
		this.name = name;
		this.posts = new ArrayList<Post>();
	}
	
	public String getId() {
		return id;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public ArrayList<Post> getPosts() {
		return posts;
	}
	public void setPosts(ArrayList<Post> posts) {
		this.posts = posts;
	}
	public void addPosts(Post post) {
		this.posts.add(post);
	}
	public int getUnread() {
		return unread;
	}
	public void setUnread(int unread) {
		this.unread = unread;
	}
	public int getCount() {
		return count;
	}
	public void setCount(int count) {
		this.count = count;
	}
	public Folder getFolder() {
		return folder;
	}
	public void setFolder(Folder folder) {
		this.folder = folder;
	}
	public String getLink() {
		return link;
	}
	public void setLink(String link) {
		this.link = link;
	}
	public String getRssLink() {
		return rssLink;
	}
	public void setRssLink(String rssLink) {
		this.rssLink = rssLink;
	}
	public void addCount(int c){
		this.unread += c;
	}
	@Override
	public String getLabel() {
		if (this.unread > 0)
			return this.name+" ("+this.unread+")";
		else
			return this.name;
	}
	
	@Override
	public String getTitle() {
		return this.name;
	}	
}
